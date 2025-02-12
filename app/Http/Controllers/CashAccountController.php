<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CashAccount;
use App\Models\CashTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CashAccountController extends Controller
{
    public function index(Request $request)
    {
        $filter_active = false;
        $is_reset = $request->get('action') == 'reset';
        $filter = [
            'search' => $request->get('search', ''),
            'active' => $is_reset ? 'all' : $request->get('active', 'all'),
            'type' => $is_reset ? 'all' : $request->get('type', 'all'),
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

    public function export(Request $request)
    {
        // ambil data akun
        $accounts = CashAccount::orderBy('id', 'asc')->get();

        if ($request->get('format') == 'pdf') {
            // Load data ke dalam tampilan PDF
            $pdf = Pdf::loadView('pages.cash-account.export.cash-account-list-pdf', compact('accounts'));

            // Unduh sebagai file PDF
            return $pdf->download('Daftar Akun Kas Digital - ' . Carbon::now()->format('dmY_His') . '.pdf');
        }

        if ($request->get('format') == 'excel') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Tambahkan header
            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', 'Jenis');
            $sheet->setCellValue('C1', 'Nama Kas');
            $sheet->setCellValue('D1', 'Bank');
            $sheet->setCellValue('E1', 'No Rekening');
            $sheet->setCellValue('F1', 'Rekening a.n');
            $sheet->setCellValue('G1', 'Status');
            $sheet->setCellValue('H1', 'Saldo');

            // Tambahkan data ke Excel
            $row = 2;
            foreach ($accounts as $account) {
                $sheet->setCellValue('A' . $row, $account->id);
                $sheet->setCellValue('B' . $row, $account->type == 'cash' ? 'Tunai' : 'Bank');
                $sheet->setCellValue('C' . $row, $account->name);
                $sheet->setCellValue('D' . $row, $account->bank);
                $sheet->setCellValue('E' . $row, $account->bank_account_name);
                $sheet->setCellValue('F' . $row, $account->bank_account_number);
                $sheet->setCellValue('G' . $row, $account->active ? 'Aktif' : 'Tidak Aktif');
                $sheet->setCellValue('H' . $row, $account->balance);
                $row++;
            }

            // Kirim ke memori tanpa menyimpan file
            $response = new StreamedResponse(function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            });

            // Atur header response untuk download
            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $response->headers->set('Content-Disposition', 'attachment; filename="Kas Digital - Daftar Akun Kas.xlsx"');

            return $response;
        }

        return abort(400, 'Format tidak didukung');
    }

    public function edit(Request $request, $id = 0)
    {
        if (!$id) {
            $item = new CashAccount();
            $item->active = true;
            $item->type = 0;
        } else {
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
