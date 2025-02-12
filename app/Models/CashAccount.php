<?php

namespace App\Models;

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
