<?php

namespace App\Models;

use Carbon\Carbon;

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
        'category_id', 'account_id', 'date', 'amount', 'description', 'notes'
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

    public static function getCashflowData(array $filter)
    {
        // Pastikan period diisi
        if (!isset($filter['period'])) {
            throw new \InvalidArgumentException("Filter 'period' wajib diisi.");
        }

        // Tentukan rentang tanggal berdasarkan periode yang dipilih
        switch ($filter['period']) {
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
        $query = self::whereBetween('date', [$startDate, $endDate]);

        if (!empty($filter['account_id']) && $filter['account_id'] !== 'all') {
            $query->where('account_id', $filter['account_id']);
        }

        // Ambil transaksi dan kelompokkan berdasarkan tanggal
        $transactions = $query->get()->groupBy(function ($trx) {
            return Carbon::parse($trx->date)->format('d'); // Format tanggal contoh: "18 Feb"
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
}
