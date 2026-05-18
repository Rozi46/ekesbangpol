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
        Schema::create('db_notifikasi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_data', 100);
            $table->string('title_notif', 200);
            $table->text('text_notif');
            $table->string('link_notif', 200);
            $table->enum('status_read', ['Yes', 'No']);
            $table->string('code_company', 100);
            $table->timestamps();

            $table->index('code_data', 'idx_notif_code_data');
            $table->index('code_company', 'idx_notif_company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('db_notifikasi');
    }
};
