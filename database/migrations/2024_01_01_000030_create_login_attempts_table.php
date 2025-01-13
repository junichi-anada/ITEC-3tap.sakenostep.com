<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('login_id', 64)->comment('試行されたログインID');
            $table->string('ip_address', 39)->comment('ログイン試行を行ったIPアドレス');
            $table->boolean('is_success')->default(false)->comment('成功フラグ');
            $table->text('failure_reason')->nullable()->comment('失敗理由');
            $table->timestamp('created_at')->comment('試行日時');

            $table->index(['login_id', 'created_at']);
            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
}; 