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
        Schema::create('db_ranks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_data', 100);
            $table->string('pangkat', 100);
            $table->string('golongan', 10);
            $table->string('ruang', 10);
            $table->enum('status_data', ['Aktif', 'Tidak Aktif']);
            $table->timestamps();            

            $table->index('code_data', 'idx_ranks_code_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('db_ranks');
    }
};
