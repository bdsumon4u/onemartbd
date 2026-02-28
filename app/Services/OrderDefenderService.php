<?php

namespace App\Services;

use App\Models\Order;
use App\Models\WebSettings;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OrderDefenderService
{
    private ?WebSettings $settings = null;

    /**
     * Check if the order should be allowed based on rate limits.
     *
     * @return array{allowed: bool, reason: string|null}
     */
    public function check(string $ip, string $phone): array
    {
        $settings = $this->getSettings();

        if (! $settings || ! $settings->is_order_defender_enabled) {
            return ['allowed' => true, 'reason' => null];
        }

        $checks = [
            ['limit' => $settings->order_limit_per_minute, 'minutes' => 1, 'label' => 'minute'],
            ['limit' => $settings->order_limit_per_hour, 'minutes' => 60, 'label' => 'hour'],
            ['limit' => $settings->order_limit_per_day, 'minutes' => 1440, 'label' => 'day'],
        ];

        if ($settings->order_defender_restrict_by_ip ?? true) {
            $ipResult = $this->checkIpLimits($ip, $checks);
            if (! $ipResult['allowed']) {
                Log::warning('Order defender limit exceeded', ['ip' => $ip, 'reason' => $ipResult['reason']]);

                return $ipResult;
            }
        }

        if ($settings->order_defender_restrict_by_phone ?? true) {
            $phoneResult = $this->checkPhoneLimits($phone, $checks);
            if (! $phoneResult['allowed']) {
                Log::warning('Order defender limit exceeded', ['ip' => $ip, 'reason' => $phoneResult['reason']]);

                return $phoneResult;
            }
        }

        if ($settings->order_defender_restrict_by_user_agent ?? true) {
            $userAgentResult = $this->checkUserAgentLimits(request()?->userAgent(), $checks, $settings);
            if (! $userAgentResult['allowed']) {
                Log::warning('Order defender limit exceeded', ['ip' => $ip, 'reason' => $userAgentResult['reason']]);

                return $userAgentResult;
            }
        }

        return ['allowed' => true, 'reason' => null];
    }

    /**
     * @param  array<int, array{limit: int|null, minutes: int, label: string}>  $checks
     * @return array{allowed: bool, reason: string|null}
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
                    ];
                }
            }
        }

        return ['allowed' => true, 'reason' => null];
    }

    /**
     * @param  array<int, array{limit: int|null, minutes: int, label: string}>  $checks
     * @return array{allowed: bool, reason: string|null}
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
                    ];
                }
            }
        }

        return ['allowed' => true, 'reason' => null];
    }

    /**
     * @param  array<int, array{limit: int|null, minutes: int, label: string}>  $checks
     * @return array{allowed: bool, reason: string|null}
     */
    private function checkUserAgentLimits(?string $userAgent, array $checks, WebSettings $settings): array
    {
        if (! $userAgent) {
            return ['allowed' => true, 'reason' => null];
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
                    ];
                }
            }
        }

        return ['allowed' => true, 'reason' => null];
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
