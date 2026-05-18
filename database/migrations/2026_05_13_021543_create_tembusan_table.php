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
        Schema::create('db_tembusan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_data', 100);
            $table->string('code_surat', 100);
            $table->integer('nomor_urut');
            $table->string('nama_tembusan', 200);
            $table->string('code_company', 100);
            $table->timestamps();

            $table->index('code_data', 'idx_tembusan_code_data');
            $table->index('code_company', 'idx_tembusan_company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('db_tembusan');
    }
};
