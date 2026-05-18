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
        Schema::create('db_employees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_data', 100);
            $table->string('nama_pegawai', 100);
            $table->string('nomor_ktp', 20);
            $table->string('agama',100);
            $table->string('nip',20);
            $table->enum('gender', ['Laki-laki', 'Perempuan']);
            $table->string('tempat_lahir',100);
            $table->date('tanggal_lahir')->nullable();
            $table->string('code_jabatan', 100);
            $table->string('pendidikan', 100);
            $table->string('jurusan', 100);
            $table->string('code_pangkat', 100);
            $table->text('alamat');
            $table->string('email', 100);
            $table->string('nomor_hp', 30);
            $table->string('photo_profil', 100);
            $table->enum('status_data', ['Aktif', 'Tidak Aktif']);
            $table->string('code_company', 100);
            $table->timestamps();            

            $table->index('code_data', 'idx_employees_code_data');
            $table->index('code_jabatan', 'idx_employees_jabatan');
            $table->index('code_pangkat', 'idx_employees_pangkat');
            $table->index('code_company', 'idx_employees_company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('db_employees');
    }
};
