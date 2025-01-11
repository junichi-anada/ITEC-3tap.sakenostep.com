<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 会社関連のテーブルを作成するマイグレーション
 */
return new class extends Migration
{
    /**
     * マイグレーションを実行
     */
    public function up(): void
    {
        // 会社テーブル
        Schema::create('companies', function (Blueprint $table) {
            $table->id()->comment('会社のユニークなID');
            $table->string('company_code', 64)->unique()->comment('会社コード（ユニーク）');
            $table->string('company_name', 32)->comment('会社名');
            $table->string('name', 64)->comment('登録者名または代表者名');
            $table->string('postal_code', 10)->nullable()->comment('郵便番号');
            $table->string('address', 128)->nullable()->comment('住所');
            $table->string('phone', 24)->nullable()->comment('電話番号');
            $table->string('phone2', 24)->nullable()->comment('補助電話番号');
            $table->string('fax', 24)->nullable()->comment('FAX番号');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');
        });

        // サイトテーブル
        Schema::create('sites', function (Blueprint $table) {
            $table->id()->comment('サイトのユニークなID');
            $table->string('site_code', 64)->unique()->comment('サイトコード（ユニーク）');
            $table->foreignId('company_id')->constrained()->comment('所属する会社ID');
            $table->string('url', 255)->unique()->comment('サイトのURL');
            $table->string('name', 64)->comment('サイト名');
            $table->string('description', 255)->nullable()->comment('サイトの説明');
            $table->boolean('is_btob')->default(false)->comment('BtoBサイトかどうか');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');
        });
    }

    /**
     * マイグレーションをロールバック
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
        Schema::dropIfExists('companies');
    }
};
