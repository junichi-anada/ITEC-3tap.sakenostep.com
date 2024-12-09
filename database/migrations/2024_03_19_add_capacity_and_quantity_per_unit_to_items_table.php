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
            $table->decimal('capacity', 10, 2)->nullable()->after('unit_price')->comment('容量');
            $table->integer('quantity_per_unit')->nullable()->after('capacity')->comment('1単位あたりの数量');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['capacity', 'quantity_per_unit']);
        });
    }
};
