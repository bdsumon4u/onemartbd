<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

class ActiveOrderEmployeeResolver
{
    public function activeEmployees(): Collection
    {
        $currentTime = Date::now()->toTimeString();

        return Employee::query()
            ->where('status', 1)
            ->where(function ($query) use ($currentTime): void {
                $query->whereNull('order_start')->orWhere('order_start', '<=', $currentTime);
            })
            ->where(function ($query) use ($currentTime): void {
                $query->whereNull('order_end')->orWhere('order_end', '>=', $currentTime);
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
