<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CashAccount;
use App\Models\CashTransaction;
use App\Models\CashTransactionCategory;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CashTransactionController extends Controller
{
    private function getTransactions($filter, &$filter_active, $apply_search = true)
    {
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


        if ($apply_search) {
            if (!empty($filter['search'])) {
                $q->where('description', 'like', '%' . $filter['search'] . '%');
            }
        }

        return $q->orderBy('id', 'desc')->paginate(10);
    }

    private function getFilter(Request $request)
    {
        $is_reset = $request->get('action') == 'reset';
        return [
            'account_id' => $is_reset ? -1 : (int)$request->get('account_id', -1),
            'category_id' => $is_reset ? -1 : (int)$request->get('category_id', -1),
            'period' => $is_reset ? 'this_month' : $request->get('period', 'this_month'),
            'type' => $is_reset ? 'all' : $request->get('type', 'all'),
            'search' => $request->get('search', ''),
        ];
    }

    public function index(Request $request)
    {
        $filter_active = false;
        $filter = $this->getFilter($request);
        $items = $this->getTransactions($filter, $filter_active);
        $accounts = CashAccount::all();
        $categories = CashTransactionCategory::all();

        return view('pages.cash-transaction.index', compact('items', 'accounts', 'filter', 'filter_active', 'categories'));
    }

    public function export(Request $request)
    {
        $filter = $this->getFilter($request);
        $items = $this->getTransactions($filter, $filter_activ, false);

        if ($request->get('format') == 'pdf') {
            // Load data ke dalam tampilan PDF
            //return view('pages.cash-transaction.export.cash-transaction-list-pdf', compact('items'));
            $pdf = Pdf::loadView('pages.cash-transaction.export.cash-transaction-list-pdf', compact('items'));

            // Unduh sebagai file PDF
            return $pdf->download('Daftar Transaksi Kas Digital - ' . Carbon::now()->format('dmY_His') . '.pdf');
        }

        if ($request->get('format') == 'excel') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Tambahkan header
            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', 'Tanggal');
            $sheet->setCellValue('C1', 'Jenis Transaksi');
            $sheet->setCellValue('D1', 'ID Akun');
            $sheet->setCellValue('E1', 'Nama Akun');
            $sheet->setCellValue('F1', 'ID Kategori');
            $sheet->setCellValue('G1', 'Nama Kategori');
            $sheet->setCellValue('H1', 'Deskripsi');
            $sheet->setCellValue('I1', 'Jumlah');
            $sheet->setCellValue('J1', 'Catatan');

            // Tambahkan data ke Excel
            $row = 2;
            foreach ($items as $item) {
                $sheet->setCellValue('A' . $row, $item->id);
                $sheet->setCellValue('B' . $row, $item->date);
                $sheet->setCellValue('C' . $row, $item->amount > 0 ? 'Pemasukan' : 'Pengeluaran');
                $sheet->setCellValue('D' . $row, $item->account->id);
                $sheet->setCellValue('E' . $row, $item->account->name);
                $sheet->setCellValue('F' . $row, $item->category->id);
                $sheet->setCellValue('G' . $row, $item->category->name);
                $sheet->setCellValue('H' . $row, $item->description);
                $sheet->setCellValue('I' . $row, $item->amount);
                $sheet->setCellValue('J' . $row, $item->notes);
                $row++;
            }

            // Kirim ke memori tanpa menyimpan file
            $response = new StreamedResponse(function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            });

            // Atur header response untuk download
            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $response->headers->set('Content-Disposition', 'attachment; filename="Kas Digital - Daftar Transaksi.xlsx"');

            return $response;
        }

        return abort(400, 'Format tidak didukung');
    }

    public function edit(Request $request, $id = 0)
    {
        if (!$id) {
            $item = new CashTransaction();
            $item->date = current_date();
        } else {
            if (!Auth::user()->is_admin) {
                return abort(403, "AKSES DITOLAK");
            }

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

            return redirect('cash-transaction')->with('info', 'Transaksi telah disimpan.');
        }
        $categories = CashTransactionCategory::orderBy('id', 'asc')->get();
        $accounts = CashAccount::where('active', '=', 1)->orderBy('name', 'asc')->get();
        return view('pages.cash-transaction.edit', compact('item', 'categories', 'accounts'));
    }

    public function transfer(Request $request)
    {
        $from = new CashTransaction();
        $from->date = current_date();

        $to = new CashTransaction();
        $to->date = $from->date;

        if ($request->method() == 'POST') {
            $data = $request->all();
            $data['amount'] = abs(number_from_input($request->amount));
            if (empty($data['category_id'])) {
                $data['category_id'] = null;
            }

            $validator = Validator::make($data, [
                'date' => 'required|date',
                'from_account_id' => 'required|exists:cash_accounts,id',
                'to_account_id' => 'required|exists:cash_accounts,id|different:from_account_id',
                'description' => 'required|string',
                'amount' => 'required|numeric|gt:0',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator);
            }

            DB::beginTransaction();

            try {
                $from->fill($data);
                $from->account_id = $data['from_account_id'];
                $from->amount = -$data['amount'];
                $from->save();

                $to->fill($data);
                $to->account_id = $data['to_account_id'];
                $to->amount = $data['amount'];
                $to->save();

                $fromAccount = CashAccount::findOrFail($from->account_id);
                $toAccount = CashAccount::findOrFail($to->account_id);

                $fromAccount->balance -= $data['amount'];
                $toAccount->balance += $data['amount'];

                $fromAccount->save();
                $toAccount->save();

                DB::commit();

                return redirect('cash-transaction')->with('info', 'Transaksi transfer telah disimpan.');
            } catch (\Exception $e) {
                dd($e);
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }

        $categories = CashTransactionCategory::orderBy('id', 'asc')->get();
        $accounts = CashAccount::where('active', '=', 1)->orderBy('name', 'asc')->get();
        return view('pages.cash-transaction.transfer', compact('from', 'to', 'categories', 'accounts'));
    }

    public function delete($id)
    {
        if (!Auth::user()->is_admin) {
            return abort(403, "AKSES DITOLAK");
        }

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
