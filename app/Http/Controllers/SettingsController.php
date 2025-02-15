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
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    public function __construct()
    {
        if (!Auth::user()->is_admin) {
            return abort(403, "AKSES DITOLAK");
        }
    }


    public function edit(Request $request)
    {
        return view('pages.settings.edit');
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required'
        ], [
            'company_name.required' => 'Nama Perusahaan harus diisi.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $oldValues = Setting::values();

        DB::beginTransaction();
        // inventory
        Setting::setValue('inv.show_barcode', $request->post('inv_show_barcode', false));
        Setting::setValue('inv.show_description', $request->post('inv_show_description', false));
        // app
        Setting::setValue('company.name', $request->post('company_name', ''));
        Setting::setValue('company.address', $request->post('company_address', ''));
        Setting::setValue('company.phone', $request->post('company_phone', ''));
        Setting::setValue('company.owner', $request->post('company_owner', ''));
        Setting::setValue('company.headline', $request->post('company_headline', ''));
        Setting::setValue('company.website', $request->post('company_website', ''));
        DB::commit();

        $data = [
            'Old Value' => $oldValues,
            'New Value' => Setting::values(),
        ];

        return redirect('settings')->with('info', 'Pengaturan telah disimpan.');
    }
}
