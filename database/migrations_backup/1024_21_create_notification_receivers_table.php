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
        Schema::create('notification_receivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained('notifications')->onDelete('cascade');
            $table->string('entity_type', 64);
            $table->unsignedBigInteger('entity_id');
            $table->foreignId('send_method_id')->constrained('notification_send_methods')->onDelete('cascade');
            $table->dateTime('sent_at');
            $table->boolean('is_read')->default(false);
            $table->dateTime('read_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['entity_type', 'entity_id'], 'idx_cp_notification_receivers');
            $table->index('notification_id', 'idx_fk_notification_receivers_notification_id');
            $table->index('send_method_id', 'idx_col_notification_receivers_send_method');
            $table->index('sent_at', 'idx_col_notification_receivers_sent_at');
            $table->index('read_at', 'idx_col_notification_receivers_checked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_receivers');
    }
};
