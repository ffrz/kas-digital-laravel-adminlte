<?php

namespace Database\Seeders;

use App\Models\CashTransactionCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

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
