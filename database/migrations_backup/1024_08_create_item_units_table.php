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
        Schema::create('item_units', function (Blueprint $table) {
            $table->id();
            $table->string('unit_code', 64)->unique();
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->string('name', 64);
            $table->integer('priority')->default(1);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->unique('unit_code', 'idx_uq_item_units_unit_code');
            $table->index('site_id', 'idx_fk_item_units_site_id');
            $table->index('name', 'idx_uq_item_units_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('items', 'item_units') && Schema::hasIndex('items', 'items_unit_id_foreign')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropIndex(['item_units'], 'items_unit_id_foreign');
            });
        }
        Schema::dropIfExists('item_units');
    }
};
