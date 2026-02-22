<?php

namespace App\Services;

use App\Models\Order;
use App\Models\WebSettings;
use Illuminate\Support\Carbon;
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

        $ipResult = $this->checkIpLimits($ip, $settings);
        if (! $ipResult['allowed']) {
            $this->handleLimitExceeded($ip, $settings, $ipResult['reason']);

            return $ipResult;
        }

        $phoneResult = $this->checkPhoneLimits($phone, $settings);
        if (! $phoneResult['allowed']) {
            $this->handleLimitExceeded($ip, $settings, $phoneResult['reason']);

            return $phoneResult;
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
     * @return array{allowed: bool, reason: string|null, should_flag_fake: bool}
     */
    private function checkIpLimits(string $ip, WebSettings $settings): array
    {
        $checks = [
            ['limit' => $settings->order_limit_per_ip_per_minute, 'minutes' => 1, 'label' => 'minute'],
            ['limit' => $settings->order_limit_per_ip_per_hour, 'minutes' => 60, 'label' => 'hour'],
            ['limit' => $settings->order_limit_per_ip_per_day, 'minutes' => 1440, 'label' => 'day'],
        ];

        foreach ($checks as $check) {
            if ($check['limit'] && $check['limit'] > 0) {
                $count = $this->countOrdersByIp($ip, $check['minutes']);

                if ($count >= $check['limit']) {
                    $reason = "IP limit exceeded: {$count}/{$check['limit']} orders per {$check['label']}";

                    return [
                        'allowed' => false,
                        'reason' => $reason,
                        'should_flag_fake' => (bool) $settings->auto_flag_fake_on_limit,
                    ];
                }
            }
        }

        return ['allowed' => true, 'reason' => null, 'should_flag_fake' => false];
    }

    /**
     * @return array{allowed: bool, reason: string|null, should_flag_fake: bool}
     */
    private function checkPhoneLimits(string $phone, WebSettings $settings): array
    {
        $checks = [
            ['limit' => $settings->order_limit_per_phone_per_minute, 'minutes' => 1, 'label' => 'minute'],
            ['limit' => $settings->order_limit_per_phone_per_hour, 'minutes' => 60, 'label' => 'hour'],
            ['limit' => $settings->order_limit_per_phone_per_day, 'minutes' => 1440, 'label' => 'day'],
        ];

        foreach ($checks as $check) {
            if ($check['limit'] && $check['limit'] > 0) {
                $count = $this->countOrdersByPhone($phone, $check['minutes']);

                if ($count >= $check['limit']) {
                    $reason = "Phone limit exceeded: {$count}/{$check['limit']} orders per {$check['label']}";

                    return [
                        'allowed' => false,
                        'reason' => $reason,
                        'should_flag_fake' => (bool) $settings->auto_flag_fake_on_limit,
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

        $ipResult = $this->checkIpLimits($ip, $settings);
        if ($ipResult['should_flag_fake']) {
            return ['should_flag_fake' => true, 'reason' => $ipResult['reason']];
        }

        $phoneResult = $this->checkPhoneLimits($phone, $settings);
        if ($phoneResult['should_flag_fake']) {
            return ['should_flag_fake' => true, 'reason' => $phoneResult['reason']];
        }

        return ['should_flag_fake' => false, 'reason' => null];
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
