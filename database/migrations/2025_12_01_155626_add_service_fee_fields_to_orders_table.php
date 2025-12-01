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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('service_fee_rate', 5, 2)->default(0.00)->after('tax_amount')->comment('Service fee rate percentage applied to this order');
            $table->decimal('service_fee_amount', 10, 2)->default(0.00)->after('service_fee_rate')->comment('Calculated service fee amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['service_fee_rate', 'service_fee_amount']);
        });
    }
};
