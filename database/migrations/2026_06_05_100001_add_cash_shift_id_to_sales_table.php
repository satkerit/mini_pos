<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('cash_shift_id')->nullable()->after('branch_id')->constrained()->nullOnDelete();
            $table->index('cash_shift_id');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['cash_shift_id']);
            $table->dropIndex(['cash_shift_id']);
            $table->dropColumn('cash_shift_id');
        });
    }
};
