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
        Schema::create('db_pelajar', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_data', 100);
            $table->string('nama_depan', 100);
            $table->string('nama_belakang', 100)->nullable();
            $table->string('nomor_ktp', 100);
            $table->string('agama', 100);
            $table->enum('gender', ['Laki-Laki', 'Perempuan']);
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->string('pekerjaan', 100);
            $table->text('alamat');
            $table->string('photo_profil', 100)->default('no_img');
            $table->string('email', 100);
            $table->string('nomor_hp', 100);
            $table->enum('status_data', ['Aktif', 'Tidak Aktif']);
            $table->string('code_company', 100);
            $table->timestamps();

            $table->index('code_data', 'idx_pelajar_code_data');
            $table->index('code_company', 'idx_pelajar_company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('db_pelajar');
    }
};
