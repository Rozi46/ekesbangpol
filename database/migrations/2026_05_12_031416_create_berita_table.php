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
        Schema::create('db_berita', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_data', 100);
            $table->string('code_user', 100);
            $table->string('url_berita', 1000);
            $table->string('judul_berita', 1000);
            $table->text('isi_berita');
            $table->string('sumber_berita', 100);
            $table->string('tumb_berita', 100);
            $table->integer('jumlah_view');
            $table->enum('tipe_berita', ['Berita', 'Info', 'Pengumuman']);
            $table->enum('status_data', ['Aktif', 'Tidak Aktif']);
            $table->string('code_company', 100);
            $table->timestamps();

            $table->index('code_data', 'idx_berita_code_data');
            $table->index('code_company', 'idx_berita_company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('db_berita');
    }
};
