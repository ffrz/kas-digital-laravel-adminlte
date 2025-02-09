<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CashAccount;
use App\Models\CashTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CashAccountController extends Controller
{
    public function index()
    {
        $items = CashAccount::orderBy('name', 'asc')->get();
        return view('pages.cash-account.index', compact('items'));
    }

    public function edit(Request $request, $id = 0)
    {
        if (!$id) {
            $item = new CashAccount();
            $item->active = true;
            $item->type = 0;
        }
        else {
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
            if ($isNew || ($oldBalance != $item->balance)) {
                $amount = $item->balance - $oldBalance;
                $transaction = new CashTransaction();
                $transaction->account_id = $item->id;
                $transaction->date = current_date();
                $transaction->amount = $amount;
                $transaction->description = $isNew ? 'Saldo awal' : 'Penyesuaian saldo manual';
                $transaction->save();
            }
            DB::commit();

            return redirect('cash-account')->with('info', 'Akun telah disimpan.');
        }

        return view('pages.cash-account.edit', compact('item'));
    }

    public function delete($id)
    {
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
