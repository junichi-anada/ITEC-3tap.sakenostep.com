<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 必要に応じて既存のカラムを削除
            if (Schema::hasColumn('users', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('users', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('users', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
            if (Schema::hasColumn('users', 'password')) {
                $table->dropColumn('password');
            }
            if (Schema::hasColumn('users', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
            if (Schema::hasColumn('users', 'created_at')) {
                $table->dropColumn('created_at');
            }
            if (Schema::hasColumn('users', 'updated_at')) {
                $table->dropColumn('updated_at');
            }

            // $table->dropColumn(['name', 'email', 'email_verified_at', 'password', 'remember_token']);

            // 新しいカラムの追加
            $table->string('user_code', 64)->unique()->notNullable();
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->string('name', 64)->notNullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('address', 128)->nullable();
            $table->string('phone', 24)->nullable();
            $table->string('phone2', 24)->nullable();
            $table->string('fax', 24)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // インデックスの追加
            $table->unique('user_code', 'idx_uq_users_user_code');
            $table->index('site_id', 'idx_fk_users_site_id');
            $table->index('name', 'idx_col_users_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 追加したカラムを削除
            $table->dropUnique('idx_uq_users_user_code');
            $table->dropForeign(['site_id']);
            $table->dropColumn(['user_code', 'site_id', 'name', 'postal_code', 'address', 'phone', 'phone2', 'fax']);
            $table->dropSoftDeletes();

            // 必要に応じて元のカラムを復元
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
        });
    }
};
