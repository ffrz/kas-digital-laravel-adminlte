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
            $table->boolean('active')->default(false);
            $table->unsignedTinyInteger('type');
            $table->string('name')->unique();
            $table->string('number', 20);
            $table->string('bank');
            $table->decimal('balance', 18, 2);
            $table->text('notes')->nullabel()->default(null);
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
