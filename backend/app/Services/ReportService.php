<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;

class ReportService
{
    public function getGeneralReportData(string $period): array
    {
        $startDate = $this->getStartDate($period);
        $endDate = now();

        $inputs = Transaction::where('transaction_type', 'input')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('total_amount');

        $outputs = Transaction::where('transaction_type', 'output')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('total_amount');

        $generalSales = Transaction::where('transaction_type', 'sale')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('total_amount');

        // Sales by department
        $salesByDepartment = TransactionDetail::selectRaw('
            d.department,
            SUM(transaction_details.subtotal) as total_sales,
            SUM(transaction_details.quantity) as total_quantity
        ')
        ->join('transactions as t', 'transaction_details.fk1_id_transaction', '=', 't.id_transaction')
        ->join('departments as d', 'transaction_details.fk2_id_department', '=', 'd.id_department')
        ->where('t.transaction_type', 'sale')
        ->whereBetween('t.transaction_date', [$startDate, $endDate])
        ->groupBy('d.id_department', 'd.department')
        ->orderByDesc('total_sales')
        ->get();

        return [
            'period' => $period,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'inputs' => $inputs,
            'outputs' => $outputs,
            'general_sales' => $generalSales,
            'sales_by_department' => $salesByDepartment,
            'net_profit' => $inputs + $generalSales - $outputs,
        ];
    }

    private function getStartDate(string $period): Carbon
    {
        switch ($period) {
            case 'weekly':
                return now()->subWeek();
            case 'monthly':
                return now()->subMonth();
            case 'yearly':
                return now()->subYear();
            default:
                return now()->subWeek();
        }
    }
}
