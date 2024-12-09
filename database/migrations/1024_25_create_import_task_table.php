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
        Schema::create('import_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_code');                    // コード番号
            $table->integer('site_id');                     // サイトID
            $table->string('data_type');                    // データタイプ（customer or item）
            $table->string('file_path');                    // ファイルパス
            $table->string('status');                       // ステータス
            $table->string('status_message')->nullable();   // ステータスメッセージ
            $table->string('imported_by');                  // インポートしたユーザー
            $table->timestamp('uploaded_at');               // アップロード日時
            $table->timestamp('imported_at')->nullable();   // インポート日時
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_tasks');
    }
};
