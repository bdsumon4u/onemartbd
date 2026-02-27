<?php

namespace App\Services;

use App\Models\Order;
use App\Models\WebSettings;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderDefenderService
{
    private ?WebSettings $settings = null;

    /**
     * Check if the order should be allowed based on rate limits.
     *
     * @return array{allowed: bool, reason: string|null, should_flag_fake: bool}
     */
    public function check(string $ip, string $phone): array
    {
        $settings = $this->getSettings();

        if (! $settings || ! $settings->is_order_defender_enabled) {
            return ['allowed' => true, 'reason' => null, 'should_flag_fake' => false];
        }

        $checks = [
            ['limit' => $settings->order_limit_per_minute, 'minutes' => 1, 'label' => 'minute'],
            ['limit' => $settings->order_limit_per_hour, 'minutes' => 60, 'label' => 'hour'],
            ['limit' => $settings->order_limit_per_day, 'minutes' => 1440, 'label' => 'day'],
        ];

        if ($settings->order_defender_restrict_by_ip ?? true) {
            $ipResult = $this->checkIpLimits($ip, $checks);
            if (! $ipResult['allowed']) {
                $this->handleLimitExceeded($ip, $settings, $ipResult['reason']);

                return $ipResult;
            }
        }

        if ($settings->order_defender_restrict_by_phone ?? true) {
            $phoneResult = $this->checkPhoneLimits($phone, $checks);
            if (! $phoneResult['allowed']) {
                $this->handleLimitExceeded($ip, $settings, $phoneResult['reason']);

                return $phoneResult;
            }
        }

        if ($settings->order_defender_restrict_by_user_agent ?? true) {
            $userAgentResult = $this->checkUserAgentLimits(request()?->userAgent(), $checks, $settings);
            if (! $userAgentResult['allowed']) {
                $this->handleLimitExceeded($ip, $settings, $userAgentResult['reason']);

                return $userAgentResult;
            }
        }

        return ['allowed' => true, 'reason' => null, 'should_flag_fake' => false];
    }

    /**
     * Flag an order as fake if the defender determines it should be.
     */
    public function flagOrderIfNeeded(Order $order, string $ip, string $phone): void
    {
        $result = $this->checkPostOrder($ip, $phone);

        if ($result['should_flag_fake'] && ! $order->is_fake) {
            $order->update(['is_fake' => 1]);

            Log::warning('Order flagged as fake by OrderDefender', [
                'order_id' => $order->id,
                'ip' => $ip,
                'phone' => $phone,
                'reason' => $result['reason'],
            ]);
        }
    }

    /**
     * @param  array<int, array{limit: int|null, minutes: int, label: string}>  $checks
     * @return array{allowed: bool, reason: string|null, should_flag_fake: bool}
     */
    private function checkIpLimits(string $ip, array $checks): array
    {
        foreach ($checks as $check) {
            if ($check['limit'] && $check['limit'] > 0) {
                $count = $this->countOrdersByIp($ip, $check['minutes']);
                if ($count >= $check['limit']) {
                    return [
                        'allowed' => false,
                        'reason' => $this->formatDefenderReason($check['limit'], $check['minutes']),
                        'should_flag_fake' => (bool) ($this->getSettings()?->auto_flag_fake_on_limit ?? true),
                    ];
                }
            }
        }

        return ['allowed' => true, 'reason' => null, 'should_flag_fake' => false];
    }

    /**
     * @param  array<int, array{limit: int|null, minutes: int, label: string}>  $checks
     * @return array{allowed: bool, reason: string|null, should_flag_fake: bool}
     */
    private function checkPhoneLimits(string $phone, array $checks): array
    {
        foreach ($checks as $check) {
            if ($check['limit'] && $check['limit'] > 0) {
                $count = $this->countOrdersByPhone($phone, $check['minutes']);
                if ($count >= $check['limit']) {
                    return [
                        'allowed' => false,
                        'reason' => $this->formatDefenderReason($check['limit'], $check['minutes']),
                        'should_flag_fake' => (bool) ($this->getSettings()?->auto_flag_fake_on_limit ?? true),
                    ];
                }
            }
        }

        return ['allowed' => true, 'reason' => null, 'should_flag_fake' => false];
    }

    /**
     * Post-order check: run after order is created to decide if it should be flagged.
     *
     * @return array{should_flag_fake: bool, reason: string|null}
     */
    private function checkPostOrder(string $ip, string $phone): array
    {
        $settings = $this->getSettings();

        if (! $settings || ! $settings->is_order_defender_enabled || ! $settings->auto_flag_fake_on_limit) {
            return ['should_flag_fake' => false, 'reason' => null];
        }

        $checks = [
            ['limit' => $settings->order_limit_per_minute, 'minutes' => 1, 'label' => 'minute'],
            ['limit' => $settings->order_limit_per_hour, 'minutes' => 60, 'label' => 'hour'],
            ['limit' => $settings->order_limit_per_day, 'minutes' => 1440, 'label' => 'day'],
        ];

        if ($settings->order_defender_restrict_by_ip ?? true) {
            $ipResult = $this->checkIpLimits($ip, $checks);
            if ($ipResult['should_flag_fake']) {
                return ['should_flag_fake' => true, 'reason' => $ipResult['reason']];
            }
        }

        if ($settings->order_defender_restrict_by_phone ?? true) {
            $phoneResult = $this->checkPhoneLimits($phone, $checks);
            if ($phoneResult['should_flag_fake']) {
                return ['should_flag_fake' => true, 'reason' => $phoneResult['reason']];
            }
        }

        if ($settings->order_defender_restrict_by_user_agent ?? true) {
            $userAgentResult = $this->checkUserAgentLimits(request()?->userAgent(), $checks, $settings);
            if ($userAgentResult['should_flag_fake']) {
                return ['should_flag_fake' => true, 'reason' => $userAgentResult['reason']];
            }
        }

        return ['should_flag_fake' => false, 'reason' => null];
    }

    /**
     * @param  array<int, array{limit: int|null, minutes: int, label: string}>  $checks
     * @return array{allowed: bool, reason: string|null, should_flag_fake: bool}
     */
    private function checkUserAgentLimits(?string $userAgent, array $checks, WebSettings $settings): array
    {
        if (! $userAgent) {
            return ['allowed' => true, 'reason' => null, 'should_flag_fake' => false];
        }

        foreach ($checks as $check) {
            if ($check['limit'] && $check['limit'] > 0) {
                $cacheKey = sprintf('order_defender:ua:%s:%d', sha1($userAgent), $check['minutes']);

                if (! Cache::has($cacheKey)) {
                    Cache::put($cacheKey, 0, $check['minutes'] * 60);
                }

                $count = Cache::increment($cacheKey);

                if ($count > $check['limit']) {
                    return [
                        'allowed' => false,
                        'reason' => $this->formatDefenderReason($check['limit'], $check['minutes']),
                        'should_flag_fake' => (bool) $settings->auto_flag_fake_on_limit,
                    ];
                }
            }
        }

        return ['allowed' => true, 'reason' => null, 'should_flag_fake' => false];
    }

    /**
     * Build user-friendly Bengali message for defender limit exceeded.
     */
    private function formatDefenderReason(int $limit, int $minutes): string
    {
        $bn = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        $toBn = fn (int $n) => implode('', array_map(fn ($d) => $bn[(int) $d] ?? $d, str_split((string) $n)));

        $timePart = match ($minutes) {
            1 => '১ মিনিটে',
            60 => '১ ঘন্টায়',
            1440 => '১ দিনে',
            default => $toBn($minutes).' মিনিটে',
        };

        return "দুঃখিত! নিরাপত্তাজনিত কারণে প্রতি {$timePart} {$toBn($limit)}টির বেশি অর্ডার করা যাবে না।";
    }

    private function handleLimitExceeded(string $ip, WebSettings $settings, ?string $reason): void
    {
        Log::warning('Order defender limit exceeded', [
            'ip' => $ip,
            'reason' => $reason,
        ]);

        if ($settings->auto_block_ip_on_limit) {
            DB::table('i_p_s')
                ->where('ip_address', $ip)
                ->update(['status' => 1]);
        }
    }

    private function countOrdersByIp(string $ip, int $minutes): int
    {
        return Order::where('ip_address', $ip)
            ->where('created_at', '>=', Carbon::now()->subMinutes($minutes))
            ->count();
    }

    private function countOrdersByPhone(string $phone, int $minutes): int
    {
        return Order::where('customer_phone', $phone)
            ->where('created_at', '>=', Carbon::now()->subMinutes($minutes))
            ->count();
    }

    private function getSettings(): ?WebSettings
    {
        if ($this->settings === null) {
            $this->settings = WebSettings::find(1);
        }

        return $this->settings;
    }
}
