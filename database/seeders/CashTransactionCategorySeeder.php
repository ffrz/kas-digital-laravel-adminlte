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
            ['name' => 'Investasi'],
            ['name' => 'Operasional Usaha'],
            ['name' => 'Laba Usaha'],
            ['name' => 'Pajak Kendaran'],
            ['name' => 'Biaya Hidup'],
            ['name' => 'PBB'],
            ['name' => 'Renovasi Bangunan'],
            ['name' => 'Pemeliharaan Kendaraan'],
        ]);
    }
}
