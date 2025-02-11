<?php

namespace Database\Seeders;

use App\Models\CashTransaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CashTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $startDate = Carbon::now()->subMonths(3)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        DB::beginTransaction();
        for ($date = $startDate; $date <= $endDate; $date->addDay()) {
            for ($i = 0; $i < rand(1, 5); $i++) { // Setiap hari bisa ada 1-5 transaksi
                CashTransaction::insert([
                    'account_id' => rand(1, 2),
                    'category_id' => rand(1, 3), // Sesuaikan dengan kategori yang tersedia
                    'amount' => rand(1000, 10000000) * (rand(0, 1) ? 1 : -1), // Bisa income atau expense
                    'date' => $date->toDateString(),
                    'description' => fake()->sentence(6),
                ]);
            }
        }
        DB::commit();
    }
}
