<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

class ActiveOrderEmployeeResolver
{
    public function activeEmployees(): Collection
    {
        $currentTime = Date::now()->toTimeString();

        $activeUsers = User::query()
            ->where('role', 3)
            ->where('status', 1)
            ->where(function ($query) use ($currentTime): void {
                $query->whereNull('order_start')->orWhere('order_start', '<=', $currentTime);
            })
            ->where(function ($query) use ($currentTime): void {
                $query->whereNull('order_end')->orWhere('order_end', '>=', $currentTime);
            })
            ->get(['email', 'phone']);

        $emails = $activeUsers->pluck('email')->filter()->unique()->values();
        $phones = $activeUsers->pluck('phone')->filter()->unique()->values();

        if ($emails->isEmpty() && $phones->isEmpty()) {
            return collect();
        }

        return Employee::query()
            ->where('status', 1)
            ->where(function ($query) use ($emails, $phones): void {
                if ($emails->isNotEmpty()) {
                    $query->whereIn('email', $emails->all());
                }

                if ($phones->isNotEmpty()) {
                    if ($emails->isNotEmpty()) {
                        $query->orWhereIn('phone', $phones->all());
                    } else {
                        $query->whereIn('phone', $phones->all());
                    }
                }
            })
            ->get();
    }

    public function activeEmployeeIds(): Collection
    {
        return $this->activeEmployees()->pluck('id');
    }

    public function activeEmployeeNameMap(): Collection
    {
        return $this->activeEmployees()->pluck('name', 'id');
    }
}
