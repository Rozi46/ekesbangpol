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
        Schema::create('db_arsip_tags', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_data', 100)->unique();
            $table->string('nama_tag', 100);
            $table->string('code_company', 100);
            $table->timestamps();

            $table->index('code_data', 'idx_arsiptags_code_data');
            $table->index('code_company', 'idx_arsiptags_company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('db_arsip_tags');
    }
};
