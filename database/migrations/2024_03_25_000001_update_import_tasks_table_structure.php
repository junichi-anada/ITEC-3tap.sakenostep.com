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
        Schema::table('import_tasks', function (Blueprint $table) {
            // 進捗関連のカラムを追加
            $table->integer('total_records')->nullable();
            $table->integer('processed_records')->nullable();
            $table->integer('success_records')->nullable();
            $table->integer('error_records')->nullable();

            // エラーメッセージカラムを追加
            $table->text('error_message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('import_tasks', function (Blueprint $table) {
            $table->dropColumn([
                'total_records',
                'processed_records',
                'success_records',
                'error_records',
                'error_message'
            ]);
        });
    }
};
