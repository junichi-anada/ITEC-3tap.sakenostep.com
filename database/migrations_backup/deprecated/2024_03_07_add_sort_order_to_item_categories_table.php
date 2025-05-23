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
        Schema::table('item_categories', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('is_published');
        });

        // 既存のカテゴリに対してIDをsort_orderとして設定
        DB::statement('UPDATE item_categories SET sort_order = id WHERE sort_order = 0');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_categories', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
