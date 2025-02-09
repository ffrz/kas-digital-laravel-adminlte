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
        'name', 'type', 'bank', 'number', 'balance', 'active', 'notes'
    ];
}
