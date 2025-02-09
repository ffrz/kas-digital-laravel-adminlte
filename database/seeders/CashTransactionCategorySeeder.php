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
        CashTransactionCategory::insert(['id' => 1, 'name' => 'Pemasukan Iuran']);
        CashTransactionCategory::insert(['id' => 2, 'name' => 'Pengeluaran Lain-lain']);
        CashTransactionCategory::insert(['id' => 3, 'name' => 'Pendapatan Lain-lain']);
    }
}
