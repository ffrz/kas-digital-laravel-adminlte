<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AclResource;
use App\Models\CashTransactionCategory;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CashTransactionCategoryController extends Controller
{
    public function index()
    {
        $items = CashTransactionCategory::orderBy('name', 'asc')->get();
        return view('pages.cash-transaction-category.index', compact('items'));
    }

    public function edit(Request $request, $id = 0)
    {
        $item = $id ? CashTransactionCategory::find($id) : new CashTransactionCategory();
        if (!$item) {
            return redirect('cash-transaction-category')
                ->with('warning', 'Kategori transaksi tidak ditemukan.');
        }

        if ($request->method() == 'POST') {
            $validator = Validator::make($request->all(), [
                'T' => 'required|unique:cash_transaction_categories,name,' . $request->id . '|max:100',
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
        if (!$item = CashTransactionCategory::find($id)) {
            $message = 'Kategori tidak ditemukan.';
        } else if ($item->delete($id)) {
            $message = 'Kategori ' . e($item->name) . ' telah dihapus.';
        }

        return redirect('cash-transaction-category')->with('info', $message);
    }
}
