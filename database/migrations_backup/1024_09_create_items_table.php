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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code', 64)->unique();
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('item_categories')->onDelete('cascade');
            $table->string('maker_name', 64)->nullable();
            $table->string('name', 64);
            $table->text('description')->nullable();
            $table->decimal('unit_price', 10, 2);
            $table->foreignId('unit_id')->constrained('item_units')->onDelete('cascade');
            $table->enum('from_source', ['MANUAL', 'IMPORT']);
            $table->boolean('is_recommended')->default(false);
            $table->dateTime('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('item_code', 'idx_uq_items_item_code');
            $table->index('maker_name', 'idx_uq_items_maker_name')->fulltext();
            $table->index('name', 'idx_uq_items_name')->fulltext();
            $table->index('site_id', 'idx_fk_items_site_id');
            $table->index('category_id', 'idx_fk_items_category_id');
        });

        // 部分インデックスを作成するためのカスタムSQLを実行
        DB::statement('CREATE INDEX idx_uq_items_description ON items (description(255))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'description') && Schema::hasIndex('items', 'items_description(255)_index')) {
                Schema::table('items', function (Blueprint $table) {
                    $table->dropIndex(['description(255)'], 'idx_uq_items_description');
                });
            }
        });
        Schema::dropIfExists('items');
    }
};
