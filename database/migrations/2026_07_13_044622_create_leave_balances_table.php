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
        Schema::create('db_leave_balances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_data', 100);
            $table->string('code_pegawai', 100);
            $table->integer('tahun')->default(0);
            $table->integer('hak')->default(0);
            $table->integer('terpakai')->default(0);
            $table->integer('sisa')->default(0);
            $table->string('code_company', 100);
            $table->timestamps();            

            $table->index('code_data', 'idx_leaveBalances_code_data');
            $table->index('code_pegawai', 'idx_leaveBalances_code_pegawai');
            $table->index('code_company', 'idx_leaveBalances_code_company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('db_leave_balances');
    }
};
