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
        Schema::table('sales', function (Blueprint $table) {
            $table->string('customer_name')->nullable()->after('user_id');
            $table->string('payment_method')->nullable()->after('final_amount');
            $table->decimal('received_amount', 15, 2)->default(0)->after('payment_method');
            $table->decimal('change_amount', 15, 2)->default(0)->after('received_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['customer_name', 'payment_method', 'received_amount', 'change_amount']);
        });
    }
};
