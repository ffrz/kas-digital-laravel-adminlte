<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CashAccount;
use App\Models\CashTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('pages.report.index');
    }

    public function detail(Request $request)
    {
        $type = $request->get('type', 'all');

        if (!$request->has('period')) {
            $accounts = CashAccount::all();
            return view('pages.report.detail.index', compact('accounts', 'type'));
        }

        $period = extract_daterange_from_input($request->get('period'), date('01-m-Y') . ' - ' . date('t-m-Y'));
        $startDate = datetime_from_input($period[0]);
        $endDate = datetime_from_input($period[1]);
        $accountId = $request->get('account_id');

        $q = CashTransaction::with(['account', 'category']);
        if ($accountId != 'all') {
            $q->where('account_id', '=', $accountId);
        }

        if ($type == 'income') {
            $q->where('amount', '>', 0);
        }
        else if ($type == 'expense') {
            $q->where('amount', '<', 0);
        }

        $account = $accountId !== 'all' ? CashAccount::findOrFail($accountId) : null;
        $items = $q->whereBetween('date', [$startDate, $endDate])->orderBy('date', 'asc')->get();

        if ($request->get('format') == 'pdf') {
            $pdf = Pdf::loadView('pages.report.detail.print', compact('items', 'period', 'type', 'account'));
            return $pdf->download('Laporan Rincian Transaksi - ' . Carbon::now()->format('dmY_His') . '.pdf');
        }

        return view('pages.report.detail.print', compact('items', 'period', 'type', 'account'));
    }
}
