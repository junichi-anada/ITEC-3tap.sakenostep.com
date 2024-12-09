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
        Schema::create('notification_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64);
            $table->string('description', 255)->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('notification_categories')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            $table->index('name', 'idx_col_notification_categories_name');
            $table->index('parent_id', 'idx_fk_notification_categories_parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_categories');
    }
};
