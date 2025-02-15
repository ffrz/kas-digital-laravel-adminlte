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

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CashTransaction extends BaseModel
{
    const TYPE_INITIAL_BALANCE = 0;
    const TYPE_ADJUSTMENT = 1;
    const TYPE_INCOME = 2;
    const TYPE_EXPENSE = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'account_id',
        'date',
        'amount',
        'description',
        'notes'
    ];

    protected static $_types = [
        self::TYPE_INITIAL_BALANCE => 'Saldo Awal',
        self::TYPE_ADJUSTMENT => 'Penyesuaian Saldo',
        self::TYPE_INCOME => 'Pemasukan',
        self::TYPE_EXPENSE => 'Pengeluaran',
    ];

    public function idFormatted()
    {
        return $this->id;
        //return 'TRX-' . format_date($this->date, 'Ymd') . '-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    public static function types()
    {
        return self::$_types;
    }

    public function typeName()
    {
        return self::$_types[$this->type];
    }

    public function account()
    {
        return $this->belongsTo(CashAccount::class, 'account_id');
    }

    public function category()
    {
        return $this->belongsTo(CashTransactionCategory::class, 'category_id');
    }

    public static function getRecentTransactions($filter, $count = 5)
    {
        $q = CashTransaction::with(['account']);
        $q = static::applyAccountIdFilter($q, $filter['account_id']);
        return $q->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->limit($count)
            ->get();
    }

    public static function getCashflowData(array $filter)
    {
        $q = static::query();
        $q = static::applyPeriodFilter($q, $filter['period']);
        $q = static::applyAccountIdFilter($q, $filter['account_id']);

        // Ambil transaksi dan kelompokkan berdasarkan tanggal
        $transactions = $q->get()->groupBy(function ($trx) {
            return Carbon::parse($trx->date)->format('d'); // Format tanggal contoh: "08"
        });

        // Siapkan array data untuk grafik
        $labels = [];
        $incomes = [];
        $expenses = [];

        foreach ($transactions as $date => $trxGroup) {
            $labels[] = $date;
            $income = $trxGroup->where('amount', '>', 0)->sum('amount');
            $expense = abs($trxGroup->where('amount', '<', 0)->sum('amount')); // Convert ke positif
            $incomes[] = $income;
            $expenses[] = $expense;
        }

        return [
            'labels'   => $labels,
            'incomes'  => $incomes,
            'expenses' => $expenses,
        ];
    }

    public static function getTotalIncome($filter)
    {
        $q = static::where('amount', '>', 0);
        $q = static::applyAccountIdFilter($q, $filter['account_id'], 'account_id');
        $q = static::applyPeriodFilter($q, $filter['period']);
        return $q->sum('amount');
    }

    public static function getTopIncomes($filter, $count = 5)
    {
        $q = static::query();
        $q = static::applyAccountIdFilter($q, $filter['account_id'], 'account_id');
        $q = static::applyPeriodFilter($q, $filter['period']);
        $q->where('amount', '>', 0);
        return $q->orderBy('amount', 'desc')
            ->limit($count)
            ->get();
    }

    public static function getTopExpenses($filter, $count = 5)
    {
        $q = static::query();
        $q = static::applyAccountIdFilter($q, $filter['account_id'], 'account_id');
        $q = static::applyPeriodFilter($q, $filter['period']);
        $q->where('amount', '<', 0);
        return $q->orderBy('amount', 'asc')
            ->limit($count)
            ->get();
    }

    public static function getTotalExpense($filter)
    {
        $q = static::where('amount', '<', 0);
        $q = static::applyAccountIdFilter($q, $filter['account_id'], 'account_id');
        $q = static::applyPeriodFilter($q, $filter['period']);
        return abs($q->sum('amount'));
    }

    public static function getIncomeVsExpenseChartData($filter)
    {
        $q = static::query();
        $q = static::applyAccountIdFilter($q, $filter['account_id'], 'account_id');
        $q = static::applyPeriodFilter($q, $filter['period']);

        $transactions = $q->get();

        return [
            'labels' => ['Pemasukan', 'Pengeluaran'],
            'data' => [
                $transactions->where('amount', '>', 0)->sum('amount'),
                abs($transactions->where('amount', '<', 0)->sum('amount'))
            ],
        ];
    }

    public static function getIncomeByCategoryChartData($filter)
    {
        $q = static::where('amount', '>', 0);
        $q = static::applyAccountIdFilter($q, $filter['account_id'], 'account_id');
        $q = static::applyPeriodFilter($q, $filter['period']);
        $categories = $q->with('category')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->get();
        return [
            'labels' => $categories->map(fn($c) => optional($c->category)->name ?? 'Tanpa Kategori')->toArray(),
            'data' => $categories->pluck('total')->toArray(),
        ];
    }

    public static function getExpenseByCategoryChartData($filter)
    {
        $q = static::where('amount', '<', 0);
        $q = static::applyAccountIdFilter($q, $filter['account_id'], 'account_id');
        $q = static::applyPeriodFilter($q, $filter['period']);
        $categories = $q->with('category')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->get();
        return [
            'labels' => $categories->map(fn($c) => $c->category->name)->toArray(),
            'data' => $categories->pluck('total')->toArray(),
        ];
    }

    private static function applyAccountIdFilter($q, $accountId)
    {
        if (empty($accountId) || $accountId === 'all') {
            return $q;
        }

        return $q->where('account_id', $accountId);
    }

    private static function applyPeriodFilter($q, $period)
    {
        // Pastikan period diisi
        if (empty($period)) {
            throw new \InvalidArgumentException("Filter 'period' wajib diisi.");
        }

        // Tentukan rentang tanggal berdasarkan periode yang dipilih
        switch ($period) {
            case 'today':
                $startDate = $endDate = Carbon::today();
                break;
            case 'yesterday':
                $startDate = $endDate = Carbon::yesterday();
                break;
            case 'this_week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'prev_week':
                $startDate = Carbon::now()->subWeek()->startOfWeek();
                $endDate = Carbon::now()->subWeek()->endOfWeek();
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'prev_month':
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                break;
            default:
                throw new \InvalidArgumentException("Period tidak valid.");
        }

        // Query transaksi berdasarkan rentang tanggal dan optional filter account_id
        return $q->whereBetween('date', [$startDate, $endDate]);
    }
}
