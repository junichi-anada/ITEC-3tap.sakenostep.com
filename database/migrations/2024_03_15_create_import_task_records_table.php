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
        Schema::create('import_task_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_task_id')->constrained('import_tasks')->cascadeOnDelete();
            $table->integer('row_number');
            $table->string('status')->default('pending');
            $table->text('data')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['import_task_id', 'row_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_task_records');
    }
};
