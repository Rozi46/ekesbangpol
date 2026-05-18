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
        Schema::create('db_ormas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_data', 100);
            $table->string('nama_ormas', 100);
            $table->string('nama_singkatan', 100)->nullable();
            $table->string('nomor_skt', 100)->nullable();
            $table->date('tanggal_skt')->nullable();
            $table->date('tanggal_berlaku_skt')->nullable();
            $table->date('tanggal_berdiri')->nullable();
            $table->string('nama_notaris', 100)->nullable();
            $table->string('nomor_akta', 100)->nullable();
            $table->date('tanggal_akta')->nullable();
            $table->text('alamat_lengkap')->nullable();
            $table->string('nomor_telfon', 100)->nullable();
            $table->string('nomor_npwp', 100)->nullable();
            $table->string('nama_ketua', 100)->nullable();
            $table->string('nama_sekretaris', 100)->nullable();
            $table->string('nama_bendahara', 100)->nullable();
            $table->string('nomor_hp_ketua', 100)->nullable();
            $table->string('nomor_hp_sekreataris', 100)->nullable();
            $table->string('nomor_hp_bendahara', 100)->nullable();
            $table->string('logo_ormas', 100)->default('no_img');
            $table->text('catatan_tambaan')->nullable();
            $table->enum('status_data', ['Aktif', 'Tidak Aktif']);
            $table->string('code_company', 100);
            $table->timestamps();

            $table->index('code_data', 'idx_ormas_code_data');
            $table->index('code_company', 'idx_ormas_company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('db_ormas');
    }
};
