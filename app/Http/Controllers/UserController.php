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
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    private const VALIDATION_RULE_NAME = 'required|max:100';
    private const VALIDATION_RULE_PASSWORD = 'min:5|max:40';

    public function __construct()
    {
        if (!Auth::user()->is_admin) {
            return abort(403, "AKSES DITOLAK");
        }
    }

    /**
     * Menampilkan daftar pengguna dengan filter pencarian dan status.
     */
    public function index(Request $request)
    {
        $filter_active = false;
        $is_reset = $request->get('action') == 'reset';
        $filter = [
            'search' => $request->get('search', ''),
            'status' => $is_reset ? -1 : $request->get('status', -1),
            'type' => $is_reset ? -1 : $request->get('type', -1),
        ];
        $q = User::query();
        if ($filter['status'] != -1) {
            $filter_active = true;
            $q->where('is_active', '=', $filter['status']);
        }
        if ($filter['type'] != -1) {
            $filter_active = true;
            $q->where('is_admin', '=', $filter['type']);
        }
        if (!empty($filter['search'])) {
            $q->where(function ($query) use ($filter) {
                $query->where('username', 'like', '%' . $filter['search'] . '%')
                    ->orWhere('fullname', 'like', '%' . $filter['search'] . '%');
            });
        }
        $items = $q->orderBy('fullname', 'asc')->paginate(10);
        return view('pages.user.index', compact('items', 'filter', 'filter_active'));
    }

    /**
     * Mengekspor daftar pengguna ke dalam format PDF atau Excel.
     */
    public function export(Request $request)
    {
        // ambil data users
        $users = User::orderBy('id', 'asc')->get();

        if ($request->get('format') == 'pdf') {
            // Load data ke dalam tampilan PDF
            $pdf = Pdf::loadView('pages.user.export.user-list-pdf', compact('users'));

            // Unduh sebagai file PDF
            return $pdf->download('Daftar Pengguna Kas Digital - ' . Carbon::now()->format('dmY_His') . '.pdf');
        }

        if ($request->get('format') == 'excel') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Tambahkan header
            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', 'Username');
            $sheet->setCellValue('C1', 'Nama Lengkap');
            $sheet->setCellValue('D1', 'Hak Akses');
            $sheet->setCellValue('E1', 'Status');

            // Tambahkan data ke Excel
            $row = 2;
            foreach ($users as $user) {
                $sheet->setCellValue('A' . $row, $user->id);
                $sheet->setCellValue('B' . $row, $user->username);
                $sheet->setCellValue('C' . $row, $user->fullname);
                $sheet->setCellValue('D' . $row, $user->is_admin ? 'Administrator' : 'User');
                $sheet->setCellValue('E' . $row, $user->is_active ? 'Aktif' : 'Tidak Aktif');
                $row++;
            }

            // Kirim ke memori tanpa menyimpan file
            $response = new StreamedResponse(function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            });

            // Atur header response untuk download
            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $response->headers->set('Content-Disposition', 'attachment; filename="Kas Digital - Daftar Pengguna.xlsx"');

            return $response;
        }

        return abort(400, 'Format tidak didukung');
    }

    public function edit(Request $request, $id = 0)
    {
        $user = (int)$id == 0 ? new User() : User::find($id);

        if (!$user) {
            return redirect('user')->with('warning', 'Pengguna tidak ditemukan.');
        } else if ($user->username == 'admin') {
            return redirect('user')->with('warning', 'Akun <b>' . $user->username . '</b> tidak boleh diubah.');
        }

        if ($request->isMethod('post')) {
            $rules = ['fullname' => self::VALIDATION_RULE_NAME];

            if (!$id) {
                $rules['username'] = 'required|unique:users,username,' . $id . '|min:3|max:40';
                $rules['password'] = 'required|' . self::VALIDATION_RULE_PASSWORD;
            } else if (!empty($request->password)) {
                $rules['password'] = self::VALIDATION_RULE_PASSWORD;
            }

            $data = $request->all();
            $request->validate($rules);
            fill_with_default_value($data, ['is_active', 'is_admin'], false);
            if (empty($request->password)) {
                unset($data['password']);
            }
            $user->fill($data);

            if (!$id) {
                $message = 'Akun pengguna <b>' . $data['username'] . '</b> telah dibuat.';
            } else {
                $message = 'Akun pengguna <b>' . $data['username'] . '</b> telah diperbarui.';
            }

            $user->save();

            return redirect('user')->with('info', $message);
        }

        return view('pages.user.edit', compact('user'));
    }

    /**
     * Menampilkan dan menyimpan perubahan profil pengguna.
     */
    public function profile(Request $request)
    {
        if (!$user = User::find(Auth::user()->id)) {
            return redirect('login');
        }

        if ($request->isMethod('post')) {
            $changedFields = ['fullname'];
            $rules = [
                'fullname' => self::VALIDATION_RULE_NAME,
            ];

            if (!empty($request->current_password)) {
                $changedFields[] = 'password';
                $rules['current_password'] = 'required';
                $rules['password'] = self::VALIDATION_RULE_PASSWORD . '|confirmed';
                $rules['password_confirmation'] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if (in_array('password', $changedFields)) {
                if (!Hash::check($request->current_password, $user->password)) {
                    $validator->errors()->add('current_password', 'Kata sandi yang anda masukkan anda salah.');
                    return redirect()->back()->withInput()->withErrors($validator);
                }
            }

            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator);
            }

            $user->update($request->only($changedFields));

            return redirect('user/profile')->with('info', 'Profil anda telah diperbarui.');
        }

        return view('pages.user.profile', compact('user'));
    }

    /**
     * Menghapus pengguna kecuali admin utama.
     */
    public function delete(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->username == 'admin') {
            return redirect('user')->with('error', 'Akun <b>' . e($user->username) . '</b> tidak boleh dihapus.');
        } else if ($user->id == Auth::user()->id) {
            return redirect('user')->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        if ($request->isMethod('post')) {
            $user->delete();
            $message = 'Akun pengguna <b>' . e($user->username) . '</b> telah dihapus.';
            return redirect('user')->with('info', $message);
        }

        return view('pages.user.delete', compact('user'));
    }
}
