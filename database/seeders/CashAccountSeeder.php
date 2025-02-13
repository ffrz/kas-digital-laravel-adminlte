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

namespace Database\Seeders;

use App\Models\CashAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CashAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CashAccount::insert([
            'name' => 'Kas Bendahara',
            'type' => 'cash',
            'balance' => 0,
            'active' => true,
        ]);

        CashAccount::insert([
            'name' => 'Rek Mandiri',
            'type' => 'bank',
            'bank' => 'Mandiri',
            'bank_account_number' => '1340001231234',
            'bank_account_name' => 'Sunda Palapa',
            'balance' => 0,
            'active' => true,
        ]);
    }
}
