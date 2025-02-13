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
use App\Models\CashTransaction;
use App\Models\CashTransactionCategory;
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

        $incomeCategories = CashTransactionCategory::where('type', 'income')->pluck('id')->toArray();
        $expenseCategories = CashTransactionCategory::where('type', 'expense')->pluck('id')->toArray();

        DB::beginTransaction();
        for ($date = $startDate; $date <= $endDate; $date->addDay()) {
            for ($i = 0; $i < rand(1, 5); $i++) { // Setiap hari bisa ada 1-5 transaksi
                $isIncome = rand(0, 4) < 2; // 2/5 pemasukan, 3/5 pengeluaran
                $amount = $isIncome
                    ? rand(1000, 5000000) // Maksimum pemasukan 5 juta
                    : -rand(1000, 3000000); // Maksimum pengeluaran 3 juta

                $categoryId = $isIncome
                    ? ($incomeCategories ? $incomeCategories[array_rand($incomeCategories)] : null)
                    : ($expenseCategories ? $expenseCategories[array_rand($expenseCategories)] : null);

                CashTransaction::insert([
                    'account_id' => rand(1, 2),
                    'category_id' => $categoryId,
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
