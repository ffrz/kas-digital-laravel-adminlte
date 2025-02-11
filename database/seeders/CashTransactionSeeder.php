<?php

namespace Database\Seeders;

use App\Models\CashAccount;
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
                $isIncome = rand(0, 3) !== 0; // 75% income, 25% expense
                $amount = $isIncome
                    ? rand(1000, 10000000) // Pemasukan lebih besar
                    : -rand(1000, 5000000); // Pengeluaran maksimal hanya 5 juta

                CashTransaction::insert([
                    'account_id' => rand(1, 2),
                    'category_id' => rand(1, 3), // Sesuaikan dengan kategori yang tersedia
                    'amount' => $amount,
                    'date' => $date->toDateString(),
                    'description' => fake()->sentence(6),
                ]);
            }
        }
        // Hitung total saldo setiap akun berdasarkan transaksi
        $balances = CashTransaction::selectRaw('account_id, SUM(amount) as total_balance')
            ->groupBy('account_id')
            ->get();

        // Update saldo di tabel CashAccount
        foreach ($balances as $balance) {
            CashAccount::where('id', $balance->account_id)->update(['balance' => $balance->total_balance]);
        }
        DB::commit();
    }
}
