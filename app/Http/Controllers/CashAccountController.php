<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CashAccount;
use App\Models\CashTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CashAccountController extends Controller
{
    public function index(Request $request)
    {
        $filter_active = false;
        $filter = [
            'search' => $request->get('search', ''),
            'active' => $request->get('active', 'all'),
            'type' => $request->get('type', 'all'),
        ];

        $q = CashAccount::query();

        if (!empty($filter['search'])) {
            $q->where('name', 'like', '%' . $filter['search'] . '%');
            $q->orWhere('bank', 'like', '%' . $filter['search'] . '%');
            $q->orWhere('bank_account_name', 'like', '%' . $filter['search'] . '%');
            $q->orWhere('bank_account_number', 'like', '%' . $filter['search'] . '%');
        }
        if ($filter['active'] != 'all') {
            $filter_active = true;
            $q->where('active', '=', $filter['active']);
        }
        if ($filter['type'] != 'all') {
            $filter_active = true;
            $q->where('type', '=', $filter['type']);
        }
        $items = $q->orderBy('name', 'asc')->paginate(10);

        return view('pages.cash-account.index', compact('items', 'filter', 'filter_active'));
    }

    public function edit(Request $request, $id = 0)
    {
        if (!$id) {
            $item = new CashAccount();
            $item->active = true;
            $item->type = 0;
        }
        else {
            if (!Auth::user()->is_admin) {
                return abort(403, "AKSES DITOLAK");
            }

            $item = CashAccount::findOrFail($id);
        }

        if ($request->method() == 'POST') {
            $data = $request->all();
            $validator = Validator::make($data, [
                'name' => 'required|unique:cash_accounts,name,' . $request->id . '|max:100',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator);
            }

            $oldBalance = $item->balance;
            $item->fill($data);
            $item->balance = number_from_input($request->balance);

            $isNew = !$item->id;

            DB::beginTransaction();
            $item->save();
            // if ($isNew || ($oldBalance != $item->balance)) {
            //     $amount = $item->balance - $oldBalance;
            //     $transaction = new CashTransaction();
            //     $transaction->account_id = $item->id;
            //     $transaction->date = current_date();
            //     $transaction->amount = $amount;
            //     $transaction->description = $isNew ? 'Saldo awal' : 'Penyesuaian saldo manual';
            //     $transaction->save();
            // }
            DB::commit();

            return redirect('cash-account')->with('info', 'Akun telah disimpan.');
        }

        return view('pages.cash-account.edit', compact('item'));
    }

    public function delete($id)
    {
        if (!Auth::user()->is_admin) {
            return abort(403, "AKSES DITOLAK");
        }

        $redirect_url = 'cash-account';

        if (!$item = CashAccount::find($id)) {
            return redirect($redirect_url)->with('warning', 'Akun tidak ditemukan.');
        }

        if (CashTransaction::where('account_id', $item->id)->count() > 0) {
            return redirect($redirect_url)
                ->with('error', 'Akun ' . e($item->name) . ' tidak dapat dihapus karena sudah digunakan di transaksi.');
        }

        $item->delete();
        return redirect($redirect_url)->with('info', 'Akun ' . e($item->name) . ' telah dihapus.');
    }
}
