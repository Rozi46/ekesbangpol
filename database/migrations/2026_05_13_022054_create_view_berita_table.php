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
        Schema::create('db_view_berita', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_data', 100);
            $table->string('code_berita', 100);
            $table->string('ip_view', 100);
            $table->string('code_company', 100);
            $table->timestamps();

            $table->index('code_data', 'idx_view_code_data');
            $table->index('code_data', 'idx_view_code_berita');
            $table->index('code_company', 'idx_view_company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('db_view_berita');
    }
};
