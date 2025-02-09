<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CashTransactionCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CashTransactionCategoryController extends Controller
{
    public function index(Request $request)
    {
        $filter = [
            'search' => $request->get('search', ''),
        ];

        $q = CashTransactionCategory::query();

        if (!empty($filter['search'])) {
            $q->where('name', 'like', '%' . $filter['search'] . '%');
            $q->orWhere('description', 'like', '%' . $filter['search'] . '%');
        }

        $items = $q->orderBy('name', 'asc')->paginate(10);

        return view('pages.cash-transaction-category.index', compact('items', 'filter'));
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
        if (!$item = CashTransactionCategory::find($id)) {
            $message = 'Kategori tidak ditemukan.';
        } else if ($item->delete($id)) {
            $message = 'Kategori ' . e($item->name) . ' telah dihapus.';
        }

        return redirect('cash-transaction-category')->with('info', $message);
    }
}
