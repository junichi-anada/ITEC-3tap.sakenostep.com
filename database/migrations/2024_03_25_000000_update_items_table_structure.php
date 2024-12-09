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
        Schema::table('items', function (Blueprint $table) {
            // 容量と単位あたりの数量を追加
            $table->decimal('capacity', 10, 2)->nullable();
            $table->integer('quantity_per_unit')->nullable();

            // デフォルト値の追加
            $table->boolean('is_active')->default(true);
            $table->boolean('is_stock_managed')->default(false);

            // unit_idをnullable に変更
            $table->foreignId('unit_id')->nullable()->change();

            // JANコードの追加
            $table->string('jan_code', 13)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn([
                'capacity',
                'quantity_per_unit',
                'jan_code'
            ]);

            // デフォルト値を元に戻す
            $table->boolean('is_active')->default(null)->change();
            $table->boolean('is_stock_managed')->default(null)->change();

            // unit_idを非nullableに戻す
            $table->foreignId('unit_id')->nullable(false)->change();
        });
    }
};
