<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 商品関連のテーブルを作成するマイグレーション
 */
return new class extends Migration
{
    /**
     * マイグレーションを実行
     */
    public function up(): void
    {
        // 商品カテゴリテーブル
        Schema::create('item_categories', function (Blueprint $table) {
            $table->id()->comment('商品カテゴリのユニークなID');
            $table->string('category_code', 64)->unique()->comment('カテゴリコード（ユニーク）');
            $table->foreignId('site_id')->constrained()->comment('商品カテゴリが所属するサイトID');
            $table->string('name', 64)->comment('商品カテゴリ名');
            $table->integer('priority')->default(1)->comment('カテゴリの優先順位');
            $table->boolean('is_published')->default(false)->comment('公開状態');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');
        });

        // 商品単位テーブル
        Schema::create('item_units', function (Blueprint $table) {
            $table->id()->comment('商品単位のユニークなID');
            $table->string('unit_code', 64)->unique()->comment('商品単位コード（ユニーク）');
            $table->foreignId('site_id')->constrained()->comment('商品単位が所属するサイトID');
            $table->string('name', 64)->comment('商品単位名');
            $table->integer('priority')->default(1)->comment('単位の優先順位');
            $table->boolean('is_published')->default(false)->comment('公開状態');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');
        });

        // 商品テーブル
        Schema::create('items', function (Blueprint $table) {
            $table->id()->comment('商品のユニークなID');
            $table->string('item_code', 64)->unique()->comment('商品コード（ユニーク）');
            $table->foreignId('site_id')->constrained()->comment('商品が所属するサイトID');
            $table->foreignId('category_id')->constrained('item_categories')->comment('商品カテゴリID');
            $table->string('maker_name', 64)->nullable()->comment('メーカー名');
            $table->string('name', 64)->comment('商品名');
            $table->text('description')->nullable()->comment('商品の説明');
            $table->decimal('unit_price', 10, 2)->comment('商品の単価');
            $table->foreignId('unit_id')->constrained('item_units')->comment('商品単位ID');
            $table->enum('from_source', ['MANUAL', 'IMPORT'])->comment('商品登録のソース');
            $table->boolean('is_recommended')->default(false)->comment('推奨商品かどうか');
            $table->timestamp('published_at')->nullable()->comment('公開日時');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');

            $table->index('site_id');
            $table->index('category_id');
            $table->index('unit_id');
        });
    }

    /**
     * マイグレーションをロールバック
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
        Schema::dropIfExists('item_units');
        Schema::dropIfExists('item_categories');
    }
};
