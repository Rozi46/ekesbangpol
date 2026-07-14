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
        Schema::create('db_leave_approvals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_data', 100);
            $table->string('code_pengajuan', 100);
            $table->string('level', 100);
            $table->string('code_approve', 100);
            $table->string('status', 50);
            $table->text('catatan');
            $table->string('code_user', 100);
            $table->string('code_company', 100);
            $table->timestamps();            

            $table->index('code_data', 'idx_leaveApprovals_code_data');
            $table->index('code_approve', 'idx_leaveApprovals_code_approve');
            $table->index('code_user', 'idx_leaveApprovals_code_user');
            $table->index('code_company', 'idx_leaveApprovals_code_company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('db_leave_approvals');
    }
};
