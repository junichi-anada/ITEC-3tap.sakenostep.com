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
            $table->string('jan_code', 13)->nullable()->after('item_code');
            $table->unique('jan_code', 'idx_uq_items_jan_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropUnique('idx_uq_items_jan_code');
            $table->dropColumn('jan_code');
        });
    }
};
