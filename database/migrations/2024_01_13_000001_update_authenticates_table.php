<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('authenticates', function (Blueprint $table) {
            // 既存のカラムを修正
            $table->string('password')->nullable()->change();

            // 新規カラムを追加
            $table->foreignId('provider_id')->after('password')->constrained('auth_providers')->comment('認証プロバイダID');
            $table->string('token')->nullable()->after('provider_id')->comment('認証トークン');
            $table->timestamp('token_expiry')->nullable()->after('token')->comment('トークン有効期限');

            // 不要なカラムを削除（もし存在する場合）
            $table->dropColumn(['is_locked', 'customer_code', 'phone_number']);
        });
    }

    public function down(): void
    {
        Schema::table('authenticates', function (Blueprint $table) {
            // 追加したカラムを削除
            $table->dropForeign(['provider_id']);
            $table->dropColumn(['provider_id', 'token', 'token_expiry']);

            // 元の状態に戻す
            $table->string('password')->change();
        });
    }
}; 