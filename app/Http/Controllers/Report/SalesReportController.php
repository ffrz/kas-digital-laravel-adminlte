<?php

namespace App\Http\Controllers\Admin\Report;

use App\Models\AclResource;
use App\Models\StockUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReportController extends BaseController
{
    public function netIncomeStatement(Request $request)
    {
        if (!$request->has('period')) {
            return view('pages.report.sales.net-income-statement.form');
        }

        $period = extract_daterange_from_input($request->get('period'), date('01-m-Y') . ' - ' . date('t-m-Y'));

        $startDate = datetime_from_input($period[0]);
        $endDate = datetime_from_input($period[1]);
        $status = StockUpdate::STATUS_COMPLETED;
        $sales = StockUpdate::TYPE_SALES_ORDER;

        // Disini kita baru menghitung total harga di penjualan dikurangi total modal, belum menghitung laba rugi di retur penjualan dan laba / rugi dari selisih stok
        $total_sales = DB::scalar(
            "select abs(sum(total_price)) from stock_updates where (date(datetime) between '$startDate' and '$endDate') and status=$status and type=$sales"
        );

        $total_cost = DB::scalar(
            "select abs(sum(total_cost)) from stock_updates where (date(datetime) between '$startDate' and '$endDate') and status=$status and type=$sales"
        );

        return view('pages.report.sales.net-income-statement.print', compact('total_cost', 'total_sales', 'period'));
    }

    public function detail(Request $request)
    {
        if (!$request->has('period')) {
            return view('pages.report.sales.detail.form');
        }

        $period = extract_daterange_from_input($request->get('period'), date('01-m-Y') . ' - ' . date('t-m-Y'));
        $startDate = datetime_from_input($period[0]);
        $endDate = datetime_from_input($period[1]);
        $status = StockUpdate::STATUS_COMPLETED;
        $sales = StockUpdate::TYPE_SALES_ORDER;

        // Mengambil data dari penjualan yang statusnya selesai
        $items = StockUpdate::with(['details', 'details.product'])
            ->whereRaw("(date(datetime) between '$startDate' and '$endDate') and status=$status and type=$sales")
            ->get();

        return view('pages.report.sales.detail.print2', compact('items', 'period'));
    }

    public function recap(Request $request)
    {
        if (!$request->has('period')) {
            return view('pages.report.sales.recap.form');
        }

        $period = extract_daterange_from_input($request->get('period'), date('01-m-Y') . ' - ' . date('t-m-Y'));

        $startDate = datetime_from_input($period[0]);
        $endDate = datetime_from_input($period[1]);
        $status = StockUpdate::STATUS_COMPLETED;
        $sales = StockUpdate::TYPE_SALES_ORDER;

        $tmp_items = StockUpdate::whereRaw("(date(datetime) between '$startDate' and '$endDate') and status=$status and type=$sales")->get();

        $items = [];
        foreach ($tmp_items as $item) {
            $date = explode(' ', $item->closed_datetime)[0];
            if (empty($items[$date])) {
                $items[$date] = [];
            }

            $items[$date][] = $item;
        }

        return view('pages.report.sales.recap.print', compact('items', 'period'));
    }
}
