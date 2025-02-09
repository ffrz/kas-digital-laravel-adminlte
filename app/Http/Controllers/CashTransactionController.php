<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CashAccount;
use App\Models\CashTransaction;
use App\Models\CashTransactionCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CashTransactionController extends Controller
{
    public function index(Request $request)
    {
        $filter = [
            'account_id' => (int)$request->get('account_id', $request->session()->get('cash-transaction.filter.account_id', -1)),
            'search' => $request->get('search', $request->session()->get('cash-transaction.filter.search', '')),
        ];

        $q = CashTransaction::with(['account', 'category']);
        if ($filter['account_id'] > 0) {
            $q->where('account_id', '=', $filter['account_id']);
        }

        if (!empty($filter['search'])) {
            $q->where('description', 'like', '%' . $filter['search'] . '%');
        }

        $request->session()->put('cash-transaction.filter.account_id', $filter['account_id']);
        $items = $q->orderBy('id', 'desc')->paginate(10);
        $accounts = CashAccount::all();

        return view('pages.cash-transaction.index', compact('items', 'accounts', 'filter'));
    }

    public function edit(Request $request, $id = 0)
    {
        if (!$id) {
            $item = new CashTransaction();
            $item->date = current_date();
        } else {
            $item = CashTransaction::findOrFail($id);
        }
        $item->type = $item->amount < 0 ? 'expense' : 'income';

        if ($request->method() == 'POST') {
            $validator = Validator::make($request->all(), [
                'description' => 'required',
                'date' => 'required',
                'account_id' => 'required',
            ], [
                'description.required' => 'Deskripsi harus diisi.',
                'date.required' => 'Tanggal harus diisi.',
                'account_id.required' => 'Akun harus dipilih.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator);
            }

            DB::beginTransaction();

            $data = ['Old Data' => $item->toArray()];
            if ($item->account) {
                $item->account->balance -= $item->amount;
                $item->account->save();
            }

            $item->fill($request->except('type'));
            unset($item->type);

            if (empty($item->category_id)) {
                $item->category_id = null;
            }

            $item->amount = number_from_input($item->amount);
            if ($request->type == 'expense') {
                $item->amount = -$item->amount;
            }

            $item->save();

            $account = CashAccount::find($item->account_id);
            $account->balance += $item->amount;
            $account->save();
            DB::commit();

            return redirect('cash-transaction')->with('info', 'Kategori transaksi telah disimpan.');
        }
        $categories = CashTransactionCategory::orderBy('id', 'asc')->get();
        $accounts = CashAccount::where('active', '=', 1)->orderBy('name', 'asc')->get();
        return view('pages.cash-transaction.edit', compact('item', 'categories', 'accounts'));
    }

    public function delete($id)
    {
        $item = CashTransaction::findOrFail($id);
        $account = CashAccount::find($item->account_id);
        $account->balance -= $item->amount;
        $message = 'Transaksi ' . e($item->idFormatted()) . ' telah dihapus.';

        DB::beginTransaction();
        $item->delete();
        $account->save();
        DB::commit();

        return redirect('cash-transaction')->with('info', $message);
    }
}
