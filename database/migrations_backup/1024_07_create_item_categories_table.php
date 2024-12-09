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
        Schema::create('item_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_code', 64)->unique();
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->string('name', 64);
            $table->integer('priority')->default(1);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->unique('category_code', 'idx_uq_item_categories_category_code');
            $table->index('site_id', 'idx_fk_item_categories_site_id');
            $table->index('name', 'idx_uq_item_categories_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('items', 'item_categories') && Schema::hasIndex('items', 'items_category_id_foreign')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropIndex(['item_categories'], 'items_category_id_foreign');
            });
        }
        Schema::dropIfExists('item_categories');
    }
};
