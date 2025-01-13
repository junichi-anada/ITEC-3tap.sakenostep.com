<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_code')->unique();
            $table->string('type');
            $table->string('status');
            $table->text('status_message')->nullable();
            $table->string('file_path');
            $table->unsignedBigInteger('site_id');
            $table->string('created_by');
            $table->integer('total_records')->default(0);
            $table->integer('processed_records')->default(0);
            $table->integer('success_records')->default(0);
            $table->integer('error_records')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index(['site_id', 'created_by']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_tasks');
    }
}; 