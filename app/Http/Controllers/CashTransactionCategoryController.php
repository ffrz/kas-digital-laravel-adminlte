<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CashTransactionCategory;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CashTransactionCategoryController extends Controller
{
    public function index(Request $request)
    {
        $filter_active = false;
        $is_reset = $request->get('action') == 'reset';
        $filter = [
            'search' => $request->get('search', ''),
            'type' => $is_reset ? 'all' : $request->get('type', 'all'),
        ];

        $q = CashTransactionCategory::query();

        if (!empty($filter['search'])) {
            $q->where('name', 'like', '%' . $filter['search'] . '%');
            $q->orWhere('description', 'like', '%' . $filter['search'] . '%');
        }
        if ($filter['type'] != 'all') {
            $filter_active = true;
            $q->where('type', '=', $filter['type']);
        }

        $items = $q->orderBy('name', 'asc')->paginate(10);

        return view('pages.cash-transaction-category.index', compact('items', 'filter', 'filter_active'));
    }

    public function export(Request $request)
    {
        // ambil data akun
        $categories = CashTransactionCategory::orderBy('name', 'asc')->get();

        if ($request->get('format') == 'pdf') {
            // Load data ke dalam tampilan PDF
            $pdf = Pdf::loadView('pages.cash-transaction-category.export.cash-transaction-category-list-pdf', compact('categories'));

            // Unduh sebagai file PDF
            return $pdf->download('Daftar Kategori Transaksi Kas Digital - ' . Carbon::now()->format('dmY_His') . '.pdf');
        }

        return abort(400, 'Format tidak didukung');
    }

    public function edit(Request $request, $id = 0)
    {
        $item = $id ? CashTransactionCategory::find($id) : new CashTransactionCategory();
        if (!$item) {
            return redirect('cash-transaction-category')
                ->with('warning', 'Kategori transaksi tidak ditemukan.');
        } else if (!Auth::user()->is_admin) {
            return abort(403, "AKSES DITOLAK");
        }

        if ($request->method() == 'POST') {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:cash_transaction_categories,name,' . $request->id . '|max:100',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator);
            }

            $data = $request->all();
            $item->fill($data);
            $item->save();

            return redirect('cash-transaction-category')->with('info', 'Kategori transaksi telah disimpan.');
        }

        return view('pages.cash-transaction-category.edit', compact('item'));
    }

    public function delete($id)
    {
        if (!Auth::user()->is_admin) {
            return abort(403, "AKSES DITOLAK");
        }

        if (!$item = CashTransactionCategory::find($id)) {
            $message = 'Kategori tidak ditemukan.';
        } else if ($item->delete($id)) {
            $message = 'Kategori ' . e($item->name) . ' telah dihapus.';
        }

        return redirect('cash-transaction-category')->with('info', $message);
    }
}
