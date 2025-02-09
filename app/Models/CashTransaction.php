<?php

namespace App\Models;

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
}
