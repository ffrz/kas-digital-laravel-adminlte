<?php

namespace Database\Seeders;

use App\Models\CashAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CashAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CashAccount::insert([
            'name' => 'Kas Bendahara',
            'type' => 'cash',
            'balance' => 0,
            'active' => true,
        ]);

        CashAccount::insert([
            'name' => 'Rek Mandiri',
            'type' => 'bank',
            'bank' => 'Mandiri',
            'bank_account_number' => '1340001231234',
            'bank_account_name' => 'Sunda Palapa',
            'balance' => 0,
            'active' => true,
        ]);
    }
}
