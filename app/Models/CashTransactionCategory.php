<?php

namespace App\Models;

class CashTransactionCategory extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'description'
    ];
}
