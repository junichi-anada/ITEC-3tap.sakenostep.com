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
        Schema::create('notification_senders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained('notifications')->onDelete('cascade');
            $table->string('entity_type', 64);
            $table->unsignedBigInteger('entity_id');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['entity_type', 'entity_id'], 'idx_cp_notification_senders');
            $table->index('notification_id', 'idx_fk_notification_senders_notification_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_senders');
    }
};
