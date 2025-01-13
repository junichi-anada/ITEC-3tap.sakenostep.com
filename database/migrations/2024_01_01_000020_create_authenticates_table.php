<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('authenticates', function (Blueprint $table) {
            $table->id();
            $table->string('auth_code', 64)->unique()->comment('認証コード');
            $table->foreignId('site_id')->constrained()->comment('関連するサイトID');
            $table->string('entity_type', 64)->comment('認証対象のエンティティ種別');
            $table->unsignedBigInteger('entity_id')->comment('認証対象のエンティティID');
            $table->string('login_code', 50)->comment('ログインに使用するコード');
            $table->string('password')->comment('ハッシュ化されたパスワード');
            $table->timestamp('expires_at')->comment('認証の有効期限');
            $table->timestamps();
            $table->softDeletes();

            $table->index('login_code');
            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authenticates');
    }
}; 