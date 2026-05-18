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
        Schema::create('db_setting', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_data', 100);
            $table->string('manual_book', 100);
            $table->string('file_struktur_organisasi', 100);
            $table->text('kata_sambutan');
            $table->string('code_company', 100);
            $table->timestamps();

            $table->index('code_data', 'idx_setting_code_data');
            $table->index('code_company', 'idx_setting_company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('db_setting');
    }
};
