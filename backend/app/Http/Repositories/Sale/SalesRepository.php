<?php

namespace App\Http\Repositories\Sale;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Department;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;

class SalesRepository
{
    /**
     * Get all sales aggregated by department (general report)
     * Returns total sales amount per department across all stores
     */
    public function getSalesByDepartmentGeneral()
    {
        return TransactionDetail::selectRaw('
            d.id_department,
            d.department,
            gd.g_departament as general_department,
            SUM(td.subtotal) as total_sales,
            COUNT(DISTINCT t.id_transaction) as total_transactions,
            SUM(td.quantity) as total_quantity
        ')
        ->from('transaction_details as td')
        ->join('transactions as t', 'td.fk1_id_transaction', '=', 't.id_transaction')
        ->join('departments as d', 'td.fk2_id_department', '=', 'd.id_department')
        ->join('general_deps as gd', 'd.fk1_id_general_dep', '=', 'gd.id_general_dep')
        ->groupBy('d.id_department', 'd.department', 'gd.g_departament')
        ->orderByDesc('total_sales')
        ->get();
    }

    /**
     * Get sales by department for a specific store
     * Returns total sales amount per department for a given store
     */
    public function getSalesByDepartmentByStore($storeId)
    {
        return TransactionDetail::selectRaw('
            d.id_department,
            d.department,
            gd.g_departament as general_department,
            s.id_store,
            s.store,
            SUM(td.subtotal) as total_sales,
            COUNT(DISTINCT t.id_transaction) as total_transactions,
            SUM(td.quantity) as total_quantity
        ')
        ->from('transaction_details as td')
        ->join('transactions as t', 'td.fk1_id_transaction', '=', 't.id_transaction')
        ->join('departments as d', 'td.fk2_id_department', '=', 'd.id_department')
        ->join('general_deps as gd', 'd.fk1_id_general_dep', '=', 'gd.id_general_dep')
        ->join('stores as s', 't.fk1_id_store', '=', 's.id_store')
        ->where('t.fk1_id_store', '=', $storeId)
        ->groupBy('d.id_department', 'd.department', 'gd.g_departament', 's.id_store', 's.store')
        ->orderByDesc('total_sales')
        ->get();
    }

    /**
     * Get detailed sales transactions by department
     */
    public function getTransactionsByDepartment($departmentId)
    {
        return Transaction::with(['store', 'user', 'details.department'])
            ->whereHas('details', function ($query) use ($departmentId) {
                $query->where('fk2_id_department', $departmentId);
            })
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    /**
     * Get sales summary by general department
     */
    public function getSalesByGeneralDepartment()
    {
        return TransactionDetail::selectRaw('
            gd.id_general_dep,
            gd.g_departament,
            SUM(td.subtotal) as total_sales,
            COUNT(DISTINCT t.id_transaction) as total_transactions,
            SUM(td.quantity) as total_quantity
        ')
        ->from('transaction_details as td')
        ->join('transactions as t', 'td.fk1_id_transaction', '=', 't.id_transaction')
        ->join('departments as d', 'td.fk2_id_department', '=', 'd.id_department')
        ->join('general_deps as gd', 'd.fk1_id_general_dep', '=', 'gd.id_general_dep')
        ->groupBy('gd.id_general_dep', 'gd.g_departament')
        ->orderByDesc('total_sales')
        ->get();
    }

    /**
     * Get sales by department for a specific store and date range
     */
    public function getSalesByDepartmentStoreWithDateRange($storeId, $startDate = null, $endDate = null)
    {
        $query = TransactionDetail::selectRaw('
            d.id_department,
            d.department,
            gd.g_departament as general_department,
            SUM(td.subtotal) as total_sales,
            COUNT(DISTINCT t.id_transaction) as total_transactions,
            SUM(td.quantity) as total_quantity
        ')
        ->from('transaction_details as td')
        ->join('transactions as t', 'td.fk1_id_transaction', '=', 't.id_transaction')
        ->join('departments as d', 'td.fk2_id_department', '=', 'd.id_department')
        ->join('general_deps as gd', 'd.fk1_id_general_dep', '=', 'gd.id_general_dep')
        ->where('t.fk1_id_store', '=', $storeId);

        if ($startDate) {
            $query->whereDate('t.transaction_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('t.transaction_date', '<=', $endDate);
        }

        return $query->groupBy('d.id_department', 'd.department', 'gd.g_departament')
            ->orderByDesc('total_sales')
            ->get();
    }
}
