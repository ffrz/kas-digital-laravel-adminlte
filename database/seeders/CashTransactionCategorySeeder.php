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

use App\Models\CashTransactionCategory;
use Illuminate\Database\Seeder;

class CashTransactionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CashTransactionCategory::insert([
            ['name' => 'Keuntungan Investasi', 'type' => 'income'],
            ['name' => 'Refund Income', 'type' => 'income'],
            ['name' => 'Refund Expense', 'type' => 'expense'],
            ['name' => 'Operasional Usaha', 'type' => 'expense'],
            ['name' => 'Laba Usaha', 'type' => 'income'],
            ['name' => 'Pajak Kendaran', 'type' => 'expense'],
            ['name' => 'Biaya Hidup', 'type' => 'expense'],
            ['name' => 'PBB', 'type' => 'expense'],
            ['name' => 'Renovasi Bangunan', 'type' => 'expense'],
            ['name' => 'Pemeliharaan Kendaraan', 'type' => 'expense'],
        ]);
    }
}
