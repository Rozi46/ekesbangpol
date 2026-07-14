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
        Schema::create('db_leave_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_data', 100);
            $table->string('nomor_pengajuan', 100);
            $table->string('code_cuti', 100);
            $table->date('tanggal_mulai');
            $table->date('tanggal_akhir');
            $table->integer('jumlah_hari');
            $table->string('alasan', 100);
            $table->string('alamat_selama_cuti', 250);
            $table->string('status', 50);
            $table->string('file_pendukung', 100);
            $table->string('code_user', 100);
            $table->string('code_company', 100);
            $table->timestamps();            

            $table->index('code_data', 'idx_leaveRequests_code_data');
            $table->index('code_cuti', 'idx_leaveRequests_code_cuti');
            $table->index('code_user', 'idx_leaveRequests_code_user');
            $table->index('code_company', 'idx_leaveRequests_code_company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('db_leave_requests');
    }
};
