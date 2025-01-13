<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 認証関連のテーブルを作成するマイグレーション
 */
return new class extends Migration
{
    /**
     * マイグレーションを実行
     */
    public function up(): void
    {
        // 認証プロバイダーテーブル
        Schema::create('auth_providers', function (Blueprint $table) {
            $table->id()->comment('認証プロバイダーのユニークなID');
            $table->string('provider_code', 64)->unique()->comment('プロバイダーコード（ユニーク）');
            $table->string('name', 32)->comment('認証プロバイダー名');
            $table->string('description', 255)->nullable()->comment('プロバイダーの説明');
            $table->boolean('is_enable')->default(false)->comment('利用可能かどうか');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');
        });

        // 認証テーブル
        Schema::create('authenticates', function (Blueprint $table) {
            $table->id()->comment('ユニークな認証レコードID');
            $table->string('auth_code', 64)->unique()->comment('認証コード');
            $table->foreignId('site_id')->constrained()->comment('関連するサイトID');
            $table->string('entity_type', 64)->comment('認証対象のエンティティ種別');
            $table->unsignedBigInteger('entity_id')->comment('認証対象のエンティティID');
            $table->string('login_code', 50)->comment('ログインに使用するコード');
            $table->string('password', 255)->comment('ハッシュ化されたパスワード');
            $table->timestamp('expires_at')->comment('認証の有効期限');
            $table->foreignId('provider_id')->nullable()->constrained('auth_providers')->comment('認証プロバイダID');
            $table->string('token', 255)->nullable()->comment('認証トークン（プロバイダ認証用）');
            $table->timestamp('token_expiry')->nullable()->comment('トークン有効期限（プロバイダ認証用）');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');

            $table->index('login_code');
            $table->index('password');
        });

        // 使用可能サイトテーブル
        Schema::create('usable_sites', function (Blueprint $table) {
            $table->id()->comment('ユニークな使用可能サイトのレコードID');
            $table->string('entity_type', 64)->comment('サイト利用対象のエンティティ種別');
            $table->unsignedBigInteger('entity_id')->comment('サイト利用対象のエンティティID');
            $table->foreignId('site_id')->constrained()->comment('利用可能なサイトID');
            $table->boolean('shared_login_allowed')->default(false)->comment('共有ログインの可否');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');

            $table->index(['entity_type', 'entity_id']);
        });

        // サイト認証プロバイダーテーブル
        Schema::create('site_auth_providers', function (Blueprint $table) {
            $table->id()->comment('サイトで使用可能な認証プロバイダーのレコードID');
            $table->foreignId('site_id')->constrained()->comment('関連するサイトID');
            $table->foreignId('auth_provider_id')->constrained()->comment('使用可能な認証プロバイダーID');
            $table->boolean('is_enabled')->default(true)->comment('利用可能かどうか');
            $table->timestamps();

            $table->unique(['site_id', 'auth_provider_id']);
        });
    }

    /**
     * マイグレーションをロールバック
     */
    public function down(): void
    {
        Schema::dropIfExists('site_auth_providers');
        Schema::dropIfExists('usable_sites');
        Schema::dropIfExists('authenticates');
        Schema::dropIfExists('auth_providers');
    }
};
