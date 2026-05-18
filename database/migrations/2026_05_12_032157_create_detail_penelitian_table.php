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
        Schema::create('db_detail_penelitian', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_data', 100);
            $table->string('code_surat', 100);
            $table->string('nama_depan', 100);
            $table->string('nama_belakang', 100);
            $table->string('nomor_ktp', 20);
            $table->string('agama', 20);
            $table->enum('gender', ['Laki-Laki', 'Perempuan']);
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->string('pekerjaan', 100);
            $table->string('alamat', 100);
            $table->string('code_company', 100);
            $table->timestamps();

            $table->index('code_data', 'idx_detail_code_data');
            $table->index('code_data', 'idx_detail_surat');
            $table->index('code_company', 'idx_detail_company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('db_detail_penelitian');
    }
};
