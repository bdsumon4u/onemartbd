<?php

namespace App\Http\Controllers\BackEnd\Report;

use App\Http\Controllers\Controller;
use App\Services\EmployeeRankingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeePerformanceReportController extends Controller
{
    public function __construct(private EmployeeRankingService $employeeRankingService) {}

    public function index(Request $request): View
    {
        $month = (int) ($request->input('month') ?: now()->month);
        $year = (int) ($request->input('year') ?: now()->year);

        $orderConfirmRanking = $this->employeeRankingService->monthlyOrderConfirmRanking($month, $year);
        $payrollRanking = $this->employeeRankingService->payrollPerformanceRanking($month, $year);
        $summary = $this->employeeRankingService->summary($month, $year);

        return view('backEnd.admin.reports.employee_performance.index', compact(
            'month',
            'year',
            'orderConfirmRanking',
            'payrollRanking',
            'summary',
        ));
    }
}
