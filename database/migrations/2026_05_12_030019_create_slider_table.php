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
        Schema::create('db_slider', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_data', 100);
            $table->integer('nomor_urut');
            $table->string('file_slider', 500);
            $table->text('text_slide');
            $table->string('url_action', 500);
            $table->enum('status_data', ['Aktif', 'Tidak Aktif']);
            $table->string('code_company', 100);
            $table->timestamps();

            $table->index('code_data', 'idx_slider_code_data');
            $table->index('code_company', 'idx_slider_company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('db_slider');
    }
};
