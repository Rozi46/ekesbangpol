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
        Schema::create('db_klasifikasi_arsip', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_data', 100)->unique();
            $table->string('code_klasifikasi', 100);
            $table->string('nama_klasifikasi', 200);
            $table->text('deskripsi');
            $table->integer('retensi_aktif')->default(0);
            $table->integer('retensi_inaktif')->default(0);
            $table->string('code_company', 100);
            $table->timestamps();

            $table->index('code_data', 'idx_klasifikasiarsip_code_data');
            $table->index('code_company', 'idx_klasifikasiarsip_company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('db_klasifikasi_arsip');
    }
};
