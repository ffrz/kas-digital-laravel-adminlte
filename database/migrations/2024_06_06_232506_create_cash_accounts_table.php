<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cash_accounts', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['bank', 'cash']);
            $table->string('name')->unique();
            $table->string('bank')->default('');
            $table->string('bank_account_number', 20)->default('');
            $table->string('bank_account_name')->default('');
            $table->decimal('balance', 18, 2);
            $table->boolean('active')->default(false);
            $table->text('notes')->nullable()->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_accounts');
    }
};
