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
        Schema::create('db_arsip', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_data', 100)->unique();
            $table->string('code_kategori', 100);
            $table->string('judul', 200);
            $table->date('tanggal_dokumen');
            $table->text('deskripsi');
            $table->enum('akses',['publik','internal','rahasia'])->default('internal');
            $table->string('file_path', 100);            
            $table->string('code_user', 100);
            $table->string('code_company', 100);
            $table->timestamps();

            $table->index('code_data', 'idx_arsip_code_data');
            $table->index('code_kategori', 'idx_arsip_kategori');
            $table->index('tanggal_dokumen', 'idx_arsip_tanggal_dokumen');
            $table->index('code_user', 'idx_arsip_user');
            $table->index('code_company', 'idx_arsip_company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('db_arsip');
    }
};
