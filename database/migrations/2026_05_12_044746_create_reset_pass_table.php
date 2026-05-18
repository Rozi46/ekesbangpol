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
        Schema::create('db_reset_pass', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_data', 100);
            $table->string('nama_user', 100);
            $table->string('password_view', 100);
            $table->string('password_lama', 300);
            $table->string('password_baru', 300);
            $table->string('code_company', 100);
            $table->timestamps();

            $table->index('code_data', 'idx_reset_code_data');
            $table->index('code_company', 'idx_reset_company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('db_reset_pass');
    }
};
