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
        Schema::create('db_formulir', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_data', 100);
            $table->string('code_pelajar', 100);
            $table->enum('surat_untuk', ['Sendiri', 'Orang Lain']);
            $table->string('surat_dari', 200);
            $table->string('nomor_surat', 100);
            $table->date('tanggal_surat');
            $table->text('judul_surat');
            $table->date('tanggal_mulai');
            $table->date('tanggal_akhir');
            $table->text('keterangan_surat', 100)->nullable();
            $table->string('file_ktp', 100)->default('no_img');
            $table->string('file_surat_rekom', 100)->default('no_img');
            $table->string('code_user', 100)->nullable();
            $table->date('tanggal_ambil')->nullable();
            $table->timestamp('tanggal_pengambilan')->nullable();
            $table->string('ktp_pengambil', 100)->default('no_img');
            $table->string('nama_pengambil', 100)->nullable();
            $table->text('keterangan_pengambilan')->nullable();
            $table->string('nomor_tembusan', 100)->nullable();
            $table->string('nomor_tembusan_full', 100)->nullable();
            $table->string('nama_pegawai_ttd', 100)->nullable();
            $table->string('nip_pegawai_ttd', 100)->nullable();
            $table->enum('tipe_surat', ['Penelitian', 'Magang']);
            $table->enum('status_surat', ['Input', 'Pengajuan', 'Proses', 'Pengambiilan', 'Finish', 'Batal']);
            $table->string('code_company', 100);
            $table->timestamps();

            $table->index('code_data', 'idx_formulir_code_data');
            $table->index('code_data', 'idx_formulir_code_pelajar');
            $table->index('code_company', 'idx_formulir_company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('db_formulir');
    }
};
