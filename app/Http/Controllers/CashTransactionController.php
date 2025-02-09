<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CashAccount;
use App\Models\CashTransaction;
use App\Models\CashTransactionCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CashTransactionController extends Controller
{
    public function index(Request $request)
    {
        $filter_active = false;
        $filter = [
            'account_id' => (int)$request->get('account_id', $request->session()->get('cash-transaction.filter.account_id', -1)),
            'category_id' => (int)$request->get('category_id', $request->session()->get('cash-transaction.filter.category_id', -1)),
            'period' => $request->get('period', $request->session()->get('cash-transaction.filter.period', 'all')),
            'type' => $request->get('type', $request->session()->get('cash-transaction.filter.type', 'all')),
            'search' => $request->get('search', $request->session()->get('cash-transaction.filter.search', '')),
        ];

        $q = CashTransaction::with(['account', 'category']);
        if ($filter['account_id'] > 0) {
            $filter_active = true;
            $q->where('account_id', '=', $filter['account_id']);
        }

        if ($filter['category_id'] > 0) {
            $filter_active = true;
            $q->where('category_id', '=', $filter['category_id']);
        }

        if ($filter['type'] != 'all') {
            $filter_active = true;
            if ($filter['type'] == 'income') {
                $q->where('amount', '>', 0);
            } else if ($filter['type'] == 'expense') {
                $q->where('amount', '<', 0);
            }
        }

        if ($filter['period'] !== 'all') {
            $filter_active = true;

            switch ($filter['period']) {
                case 'today':
                    $q->whereDate('date', Carbon::today());
                    break;

                case 'yesterday':
                    $q->whereDate('date', Carbon::yesterday());
                    break;

                case 'this_week':
                    $q->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;

                case 'prev_week':
                    $q->whereBetween('date', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]);
                    break;

                case 'this_month':
                    $q->whereBetween('date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                    break;

                case 'prev_month':
                    $q->whereBetween('date', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()]);
                    break;
            }
        }


        if (!empty($filter['search'])) {
            $q->where('description', 'like', '%' . $filter['search'] . '%');
        }

        $request->session()->put('cash-transaction.filter.account_id', $filter['account_id']);
        $items = $q->orderBy('id', 'desc')->paginate(10);

        $accounts = CashAccount::all();
        $categories = CashTransactionCategory::all();

        return view('pages.cash-transaction.index', compact('items', 'accounts', 'filter', 'filter_active', 'categories'));
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
