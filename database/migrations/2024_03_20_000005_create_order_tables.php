<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 注文関連のテーブルを作成するマイグレーション
 */
return new class extends Migration
{
    /**
     * マイグレーションを実行
     */
    public function up(): void
    {
        // 注文テーブル
        Schema::create('orders', function (Blueprint $table) {
            $table->id()->comment('注文のユニークなID');
            $table->string('order_code', 64)->unique()->comment('注文コード（ユニーク）');
            $table->foreignId('site_id')->constrained()->comment('注文が発生したサイトID');
            $table->foreignId('user_id')->constrained()->comment('注文を行ったユーザーID');
            $table->decimal('total_price', 10, 2)->nullable()->comment('合計金額');
            $table->decimal('tax', 8, 0)->nullable()->comment('税額');
            $table->decimal('postage', 4, 0)->nullable()->comment('送料');
            $table->timestamp('ordered_at')->nullable()->comment('注文日時');
            $table->timestamp('processed_at')->nullable()->comment('処理日時');
            $table->timestamp('exported_at')->nullable()->comment('出力日時');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');

            $table->index(['site_id', 'user_id']);
        });

        // 注文明細テーブル
        Schema::create('order_details', function (Blueprint $table) {
            $table->id()->comment('注文明細のユニークなID');
            $table->string('detail_code', 64)->unique()->comment('明細コード（ユニーク）');
            $table->foreignId('order_id')->constrained()->comment('関連する注文ID');
            $table->foreignId('item_id')->constrained()->comment('注文された商品のID');
            $table->integer('volume')->comment('注文数量');
            $table->decimal('unit_price', 10, 2)->comment('商品の単価');
            $table->string('unit_name', 64)->comment('商品単位名');
            $table->decimal('price', 10, 2)->comment('合計金額（数量 × 単価）');
            $table->decimal('tax', 4, 0)->comment('税額');
            $table->timestamp('processed_at')->nullable()->comment('処理日時');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');

            $table->index('order_id');
            $table->index('item_id');
        });
    }

    /**
     * マイグレーションをロールバック
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
        Schema::dropIfExists('orders');
    }
};
