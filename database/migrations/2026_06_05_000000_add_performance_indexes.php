<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qris_transactions', function (Blueprint $table) {
            $table->index('transaction_id');
            $table->index('status');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('status');
            $table->index('payment_method');
            $table->index(['sale_id', 'status']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->index('status');
            $table->index(['branch_id', 'status']);
            $table->index('created_at');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('is_active');
        });

        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->index('type');
            $table->index(['reference_type', 'reference_id']);
        });

        Schema::table('ingredients', function (Blueprint $table) {
            $table->index('stock');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->index(['sale_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::table('qris_transactions', function (Blueprint $table) {
            $table->dropIndex(['transaction_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['payment_method']);
            $table->dropIndex(['sale_id', 'status']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['branch_id', 'status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropIndex(['reference_type', 'reference_id']);
        });

        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropIndex(['stock']);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropIndex(['sale_id', 'product_id']);
        });
    }
};
