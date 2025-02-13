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
