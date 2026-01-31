<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('usage_counters')) {
            Schema::create('usage_counters', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
                $table->date('billing_cycle_start');
                $table->date('billing_cycle_end');
                $table->unsignedInteger('used_units')->default(0);
                $table->timestamps();
                
                $table->unique([
                    'user_id',
                    'subscription_id',
                    'billing_cycle_start',
                    'billing_cycle_end'
                ], 'unique_user_billing_cycle');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usage_counters');
    }
};
