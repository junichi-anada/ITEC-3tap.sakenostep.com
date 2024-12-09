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
            $table->integer('total_records')->default(0)->after('file_path');
            $table->integer('processed_records')->default(0)->after('total_records');
            $table->integer('success_records')->default(0)->after('processed_records');
            $table->integer('error_records')->default(0)->after('success_records');
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
                'error_records'
            ]);
        });
    }
};
