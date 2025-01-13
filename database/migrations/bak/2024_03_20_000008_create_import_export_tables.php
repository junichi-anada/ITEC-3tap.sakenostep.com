<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * インポート/エクスポート関連のテーブルを作成するマイグレーション
 */
return new class extends Migration
{
    /**
     * マイグレーションを実行
     */
    public function up(): void
    {
        // インポートタスクテーブル
        Schema::create('import_tasks', function (Blueprint $table) {
            $table->id()->comment('インポートタスクのユニークなID');
            $table->string('task_code', 64)->unique()->comment('タスクコード（ユニーク）');
            $table->foreignId('site_id')->constrained()->comment('関連するサイトID');
            $table->foreignId('operator_id')->constrained()->comment('タスクを作成したオペレータID');
            $table->string('import_type', 32)->comment('インポートの種類');
            $table->string('file_name', 255)->comment('インポートファイル名');
            $table->string('file_path', 255)->comment('インポートファイルパス');
            $table->enum('status', ['WAITING', 'PROCESSING', 'COMPLETED', 'FAILED'])->comment('タスクの状態');
            $table->text('error_detail')->nullable()->comment('エラー詳細');
            $table->timestamp('started_at')->nullable()->comment('処理開始日時');
            $table->timestamp('completed_at')->nullable()->comment('処理完了日時');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');

            $table->index(['site_id', 'import_type']);
            $table->index('status');
            $table->index('started_at');
        });

        // インポート/エクスポートログテーブル
        Schema::create('import_export_logs', function (Blueprint $table) {
            $table->id()->comment('インポート/エクスポートログのユニークなID');
            $table->string('log_code', 64)->unique()->comment('ログコード（ユニーク）');
            $table->foreignId('site_id')->constrained()->comment('関連するサイトID');
            $table->foreignId('operator_id')->constrained()->comment('操作を行ったオペレータID');
            $table->enum('operation_type', ['IMPORT', 'EXPORT'])->comment('操作種別');
            $table->string('target_type', 32)->comment('対象データ種別');
            $table->string('file_name', 255)->comment('ファイル名');
            $table->integer('record_count')->default(0)->comment('処理レコード数');
            $table->text('detail')->nullable()->comment('処理詳細');
            $table->timestamp('processed_at')->comment('処理日時');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');

            $table->index(['site_id', 'operation_type']);
            $table->index('target_type');
            $table->index('processed_at');
        });
    }

    /**
     * マイグレーションをロールバック
     */
    public function down(): void
    {
        Schema::dropIfExists('import_export_logs');
        Schema::dropIfExists('import_tasks');
    }
};
