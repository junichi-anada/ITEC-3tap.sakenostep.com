<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * お気に入り商品とお知らせのテーブルを作成するマイグレーション
 */
return new class extends Migration
{
    /**
     * マイグレーションを実行
     */
    public function up(): void
    {
        // お気に入り商品テーブル
        Schema::create('favorite_items', function (Blueprint $table) {
            $table->id()->comment('お気に入り商品のユニークなID');
            $table->foreignId('site_id')->constrained()->comment('関連するサイトID');
            $table->foreignId('user_id')->constrained()->comment('お気に入りを登録したユーザーID');
            $table->foreignId('item_id')->constrained()->comment('お気に入りに登録された商品ID');
            $table->text('memo')->nullable()->comment('メモ');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');

            $table->unique(['site_id', 'user_id', 'item_id']);
        });

        // お知らせテーブル
        Schema::create('notifications', function (Blueprint $table) {
            $table->id()->comment('お知らせのユニークなID');
            $table->string('notification_code', 64)->unique()->comment('お知らせコード（ユニーク）');
            $table->foreignId('site_id')->constrained()->comment('関連するサイトID');
            $table->string('title', 128)->comment('お知らせタイトル');
            $table->text('content')->comment('お知らせ内容');
            $table->enum('notification_type', ['NOTICE', 'IMPORTANT', 'MAINTENANCE'])->comment('お知らせ種別');
            $table->timestamp('published_at')->nullable()->comment('公開日時');
            $table->timestamp('expired_at')->nullable()->comment('有効期限');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');

            $table->index('site_id');
            $table->index('notification_type');
            $table->index('published_at');
            $table->index('expired_at');
        });

        // お知らせ既読テーブル
        Schema::create('notification_reads', function (Blueprint $table) {
            $table->id()->comment('お知らせ既読のユニークなID');
            $table->foreignId('notification_id')->constrained()->comment('関連するお知らせID');
            $table->string('entity_type', 64)->comment('既読を付けたエンティティ種別');
            $table->unsignedBigInteger('entity_id')->comment('既読を付けたエンティティID');
            $table->timestamp('read_at')->comment('既読をつけた日時');
            $table->timestamps();

            $table->unique(['notification_id', 'entity_type', 'entity_id']);
            $table->index(['entity_type', 'entity_id']);
            $table->index('read_at');
        });
    }

    /**
     * マイグレーションをロールバック
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_reads');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('favorite_items');
    }
};
