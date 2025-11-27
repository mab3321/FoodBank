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
            $table->timestamp('process_started_at')->nullable()->comment('When order status changed to PROCESS');
            $table->timestamp('ready_at')->nullable()->comment('When order status changed to READY_FOR_PICKUP');
            $table->unsignedInteger('estimated_wait_time')->nullable()->comment('Estimated preparation time in minutes');
            $table->unsignedInteger('actual_preparation_time')->nullable()->comment('Actual preparation time in minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'process_started_at',
                'ready_at',
                'estimated_wait_time',
                'actual_preparation_time'
            ]);
        });
    }
};
