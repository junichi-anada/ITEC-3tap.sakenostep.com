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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->string('detail_code', 64)->unique();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->integer('volume');
            $table->decimal('unit_price', 10, 2);
            $table->string('unit_name', 64);
            $table->decimal('price', 10, 2);
            $table->decimal('tax', 4, 0);
            $table->dateTime('processed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('detail_code', 'idx_uq_order_details_detail_code');
            $table->index(['order_id', 'item_id'], 'idx_cp_order_details');
            $table->index('unit_name', 'idx_cp_order_details_unit_name')->fulltext();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
