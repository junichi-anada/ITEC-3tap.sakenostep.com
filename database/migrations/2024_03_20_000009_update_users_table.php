<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ユーザーテーブルを更新するマイグレーション
 *
 * @description
 * - ユーザー関連の追加情報カラムを追加
 * - 所属サイトとの関連付けを追加
 * - ソフトデリート機能を追加
 */
return new class extends Migration
{
    /**
     * マイグレーションを実行
     */
    public function up(): void
    {
        // sites テーブルが存在することを確認してから外部キー制約を追加
        if (Schema::hasTable('sites')) {
            Schema::table('users', function (Blueprint $table) {
                // 既存のカラムをnullableに変更
                $table->string('email')->nullable()->change();
                $table->string('password')->nullable()->change();
                $table->rememberToken()->nullable()->change();

                // 既存のカラムの後に追加
                $table->after('password', function (Blueprint $table) {
                    $table->string('user_code', 64)->unique()->comment('ユーザーコード（ユニーク）');
                    $table->foreignId('site_id')->constrained()->comment('所属するサイトID');
                    $table->string('last_name', 32)->nullable()->comment('姓');
                    $table->string('first_name', 32)->nullable()->comment('名');
                    $table->string('last_name_kana', 32)->nullable()->comment('姓（カナ）');
                    $table->string('first_name_kana', 32)->nullable()->comment('名（カナ）');
                    $table->string('postal_code', 10)->nullable()->comment('郵便番号');
                    $table->string('address', 128)->nullable()->comment('住所');
                    $table->string('phone', 24)->nullable()->comment('電話番号');
                    $table->string('phone2', 24)->nullable()->comment('補助電話番号');
                    $table->string('fax', 24)->nullable()->comment('FAX番号');
                    $table->date('birthday')->nullable()->comment('生年月日');
                    $table->enum('gender', ['MALE', 'FEMALE', 'OTHER'])->nullable()->comment('性別');
                    $table->boolean('is_active')->default(true)->comment('アクティブ状態');
                    $table->timestamp('last_login_at')->nullable()->comment('最終ログイン日時');
                });

                // ソフトデリート追加
                $table->softDeletes()->comment('削除日時');

                // インデックスを追加
                $table->index('site_id');
                $table->index('is_active');
                $table->index('last_login_at');
            });
        }
    }

    /**
     * マイグレーションをロールバック
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // インデックスを削除
            $table->dropIndex(['site_id']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['last_login_at']);

            // 削除したカラムを復元
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            // 追加したカラムを削除
            $table->dropColumn([
                'user_code',
                'site_id',
                'last_name',
                'first_name',
                'last_name_kana',
                'first_name_kana',
                'postal_code',
                'address',
                'phone',
                'phone2',
                'fax',
                'birthday',
                'gender',
                'is_active',
                'last_login_at',
                'deleted_at'  // ソフトデリート
            ]);
        });
    }
};
