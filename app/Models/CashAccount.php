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

namespace App\Models;

use Illuminate\Support\Facades\DB;

class CashAccount extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'bank',
        'bank_account_number',
        'bank_account_name',
        'balance',
        'active',
        'notes'
    ];

    public static function getIncomeExpenseReport($startDate, $endDate)
    {
        $accounts = CashAccount::all();
        $categories = CashTransactionCategory::all();
        $categories->push((object)[
            'id' => null,
            'name' => 'Tanpa Kategori'
        ]);
        $report = [
            'incomes' => [],
            'expenses' => [],
        ];

        foreach ($accounts as $account) {
            foreach ($categories as $category) {
                $income = CashTransaction::where('account_id', $account->id)
                    ->where('category_id', $category->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->where('amount', '>', 0)
                    ->sum('amount');
                if ($income != 0) {
                    $report['incomes'][] = [
                        'account_name' => $account->name,
                        'category_name' => $category->name,
                        'total' => $income,
                    ];
                }

                $expense = CashTransaction::where('account_id', $account->id)
                    ->where('category_id', $category->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->where('amount', '<', 0)
                    ->sum('amount');
                if ($expense != 0) {
                    $report['expenses'][] = [
                        'account_name' => $account->name,
                        'category_name' => $category->name,
                        'total' => $expense,
                    ];
                }
            }
        }

        return $report;
    }

    public static function getCashFlowReport($startDate, $endDate)
    {
        return DB::table('cash_accounts')
            ->leftJoin('cash_transactions', 'cash_accounts.id', '=', 'cash_transactions.account_id')
            ->select(
                'cash_accounts.id',
                'cash_accounts.name',
                DB::raw('(SELECT COALESCE(SUM(amount), 0) FROM cash_transactions WHERE account_id = cash_accounts.id AND date < "' . $startDate . '") AS initial_balance'),
                DB::raw('COALESCE(SUM(CASE WHEN cash_transactions.amount > 0 AND date BETWEEN "' . $startDate . '" AND "' . $endDate . '" THEN cash_transactions.amount ELSE 0 END), 0) AS income'),
                DB::raw('COALESCE(SUM(CASE WHEN cash_transactions.amount < 0 AND date BETWEEN "' . $startDate . '" AND "' . $endDate . '" THEN cash_transactions.amount ELSE 0 END), 0) AS expense'),
                DB::raw('(SELECT COALESCE(SUM(amount), 0) FROM cash_transactions WHERE account_id = cash_accounts.id AND date <= "' . $endDate . '") AS final_balance')
            )
            ->groupBy('cash_accounts.id', 'cash_accounts.name')
            ->get();
    }

    public static function getAllAccounts()
    {
        return static::orderBy('name', 'asc')->get();
    }

    public static function getActiveAccounts()
    {
        return static::where('active', '1')->orderBy('name', 'asc')->get();
    }

    public static function getActiveCount()
    {
        return static::where('active', 1)->count();
    }

    public static function getTotalBalance($filter)
    {
        $q = static::query();
        $q->where('active', 1);
        $q = static::applyAccountIdFilter($q, $filter['account_id']);
        return $q->sum('balance');
    }

    public static function getAccountBalanceDistributionChartData()
    {
        $accounts = static::where('active', 1)->get(['name', 'balance']);

        $account_balance_distribution_chart_data = [
            'labels' => $accounts->pluck('name')->toArray(),
            'data' => $accounts->pluck('balance')->toArray(),
        ];

        return $account_balance_distribution_chart_data;
    }

    private static function applyAccountIdFilter($q, $accountId)
    {
        if (empty($accountId) || $accountId === 'all') {
            return $q;
        }

        return $q->where('id', $accountId);
    }
}
