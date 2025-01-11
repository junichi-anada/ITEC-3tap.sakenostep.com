<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ログ関連のテーブルを作成するマイグレーション
 */
return new class extends Migration
{
    /**
     * マイグレーションを実行
     */
    public function up(): void
    {
        // セキュリティログテーブル
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id()->comment('セキュリティログのユニークなID');
            $table->string('entity_type', 64)->comment('操作エンティティの種別');
            $table->unsignedBigInteger('entity_id')->comment('操作を行ったエンティティID');
            $table->string('action_type', 32)->comment('操作の種類');
            $table->string('action_detail', 255)->nullable()->comment('操作の詳細');
            $table->string('ip_address', 39)->comment('操作者のIPアドレス');
            $table->text('user_agent')->nullable()->comment('操作者のユーザーエージェント');
            $table->timestamp('created_at')->useCurrent()->comment('操作日時');

            $table->index(['entity_type', 'entity_id']);
            $table->index('action_type');
            $table->index('ip_address');
            $table->index('created_at');
        });

        // メッセージログテーブル
        Schema::create('message_logs', function (Blueprint $table) {
            $table->id()->comment('メッセージログのユニークなID');
            $table->string('message_code', 64)->unique()->comment('メッセージコード（ユニーク）');
            $table->foreignId('site_id')->constrained()->comment('関連するサイトID');
            $table->foreignId('operator_id')->constrained()->comment('送信したオペレータID');
            $table->foreignId('user_id')->constrained()->comment('送信先ユーザーID');
            $table->text('message_content')->comment('メッセージ内容');
            $table->timestamp('sent_at')->comment('メッセージ送信日時');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');

            $table->index(['site_id', 'operator_id']);
            $table->index(['site_id', 'user_id']);
            $table->index('sent_at');
        });
    }

    /**
     * マイグレーションをロールバック
     */
    public function down(): void
    {
        Schema::dropIfExists('message_logs');
        Schema::dropIfExists('security_logs');
    }
};
