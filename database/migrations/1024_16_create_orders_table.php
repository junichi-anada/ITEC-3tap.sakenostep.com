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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 64)->unique();
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('total_price', 10, 2)->nullable();
            $table->decimal('tax', 8, 0)->nullable();
            $table->decimal('postage', 4, 0)->nullable();
            $table->dateTime('ordered_at')->nullable();
            $table->dateTime('processed_at')->nullable();
            $table->dateTime('exported_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('order_code', 'idx_uq_orders_order_code');
            $table->index(['site_id', 'user_id'], 'idx_cp_orders');
            $table->index('ordered_at', 'idx_col_ordered_at');
            $table->index('processed_at', 'idx_col_processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
