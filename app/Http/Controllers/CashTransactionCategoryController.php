<?php

/**
 * Hak Cipta (C) 2025 Fahmi Fauzi Rahman
 * Seluruh Hak Cipta Dilindungi Undang-Undang.
 *
 * File ini bersifat rahasia dan merupakan hak milik eksklusif.
 * Dilarang keras menjual kembali aplikasi ini kepada pihak lain
 * dalam bentuk apapun.
 *
 * Penulis  : Fahmi Fauzi Rahman
 * Kontak   : fahmifauzirahman@gmail.com
 * Youtube  : https://www.youtube.com/@hobi_coding
 * Facebook : https://www.facebook.com/fahmifauzirahman
 * Instagram: https://www.instagram.com/fahmi.fauzi.rahman
 * Tiktok   : https://www.tiktok.com/@ffr__85
 * Lisensi  : Hak Milik (Proprietary)
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CashTransactionCategory;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            $q->where(function ($query) use ($filter) {
                $query->where('name', 'like', '%' . $filter['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filter['search'] . '%');
            });
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
        $categories = CashTransactionCategory::orderBy('id', 'asc')->get();

        if ($request->get('format') == 'pdf') {
            // Load data ke dalam tampilan PDF
            $pdf = Pdf::loadView('pages.cash-transaction-category.export.cash-transaction-category-list-pdf', compact('categories'));

            // Unduh sebagai file PDF
            return $pdf->download('Daftar Kategori Transaksi Kas Digital - ' . Carbon::now()->format('dmY_His') . '.pdf');
        }

        if ($request->get('format') == 'excel') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Tambahkan header
            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', 'Jenis Transaksi');
            $sheet->setCellValue('C1', 'Nama Kategori');
            $sheet->setCellValue('D1', 'Deskripsi');

            // Tambahkan data ke Excel
            $row = 2;
            foreach ($categories as $category) {
                $sheet->setCellValue('A' . $row, $category->id);
                $sheet->setCellValue('B' . $row, $category->type == 'income' ? 'Pemasukan' : 'Pengeluaran');
                $sheet->setCellValue('C' . $row, $category->name);
                $sheet->setCellValue('D' . $row, $category->description);
                $row++;
            }

            // Kirim ke memori tanpa menyimpan file
            $response = new StreamedResponse(function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            });

            // Atur header response untuk download
            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $response->headers->set('Content-Disposition', 'attachment; filename="Kas Digital - Daftar Kategori Transaksi.xlsx"');

            return $response;
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

        if ($request->isMethod('post')) {
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

        $item = CashTransactionCategory::findOrFail($id);
        $item->delete();

        return redirect('cash-transaction-category')->with('info', 'Kategori ' . e($item->name) . ' telah dihapus.');
    }
}
