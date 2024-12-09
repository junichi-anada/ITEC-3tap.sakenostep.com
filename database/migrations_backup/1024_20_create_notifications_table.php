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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('notification_code', 64)->unique();
            $table->foreignId('category_id')->constrained('notification_categories')->onDelete('cascade');
            $table->string('title', 64);
            $table->text('content');
            $table->dateTime('publish_start_at');
            $table->dateTime('publish_end_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('notification_code', 'idx_uq_notifications_notification_code');
            $table->index('category_id', 'idx_col_notifications_category_id');
            $table->index('publish_start_at', 'idx_col_notifications_publish_start_at');
            $table->index('publish_end_at', 'idx_col_notifications_publish_end_at');
            $table->index('created_at', 'idx_col_notifications_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
