<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CashAccount;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $start_datetime_this_month = date('Y-m-01 00:00:00');
        $end_datetime_this_month = date('Y-m-t 23:59:59');
        $start_date_this_month = date('Y-m-01');
        $end_date_this_month = date('Y-m-t');

        $data = [
            'active_cash_account' => CashAccount::where('active', 1)->count(),
            'total_balance' => CashAccount::where('active', 1)->sum('balance'),
            'sales_count_this_month' => 0,
            'total_sales_today' => 0,
            'sales_count_today' => 0,
            'active_sales_count' => 0,
            'total_inventory_asset' => 0,
            'total_inventory_asset_price' => 0,
            'gross_sales_this_month' => 0,
            'expenses_this_month' => 0,
        ];

        return view('pages.dashboard.index', compact('data'));
    }
}
