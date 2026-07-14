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
        Schema::create('db_leave_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_data', 100);
            $table->string('code_pengajuan', 100);
            $table->string('aksi', 100);
            $table->string('keterangan', 250);
            $table->string('code_user', 100);
            $table->string('code_company', 100);
            $table->timestamps();            

            $table->index('code_data', 'idx_leaveLogs_code_data');
            $table->index('code_pengajuan', 'idx_leaveLogs_code_pengajuan');
            $table->index('code_user', 'idx_leaveLogs_code_user');
            $table->index('code_company', 'idx_leaveLogs_code_company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('db_leave_logs');
    }
};
