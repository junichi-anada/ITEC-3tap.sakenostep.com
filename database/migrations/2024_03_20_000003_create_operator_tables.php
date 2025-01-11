<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * オペレータ関連のテーブルを作成するマイグレーション
 */
return new class extends Migration
{
    /**
     * マイグレーションを実行
     */
    public function up(): void
    {
        // オイトオペレータロールテーブル
        Schema::create('site_operator_roles', function (Blueprint $table) {
            $table->id()->comment('サイトオペレータロールのユニークなID');
            $table->string('name', 32)->comment('役割名');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');
        });

        // オペレータランクテーブル
        Schema::create('operator_ranks', function (Blueprint $table) {
            $table->id()->comment('オペレータランクのユニークなID');
            $table->string('name', 32)->comment('ランク名');
            $table->integer('priority')->default(1)->comment('ランクの優先度');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');
        });

        // オペレータテーブル
        Schema::create('operators', function (Blueprint $table) {
            $table->id()->comment('オペレータのユニークなID');
            $table->string('operator_code', 64)->unique()->comment('オペレータコード（ユニーク）');
            $table->foreignId('company_id')->constrained()->comment('所属する会社ID');
            $table->string('name', 32)->comment('オペレータ名');
            $table->foreignId('operator_rank_id')->constrained()->comment('オペレータランクID');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');

            $table->index('company_id');
            $table->index('operator_rank_id');
        });

        // サイトオペレータテーブル
        Schema::create('site_operators', function (Blueprint $table) {
            $table->id()->comment('サイトオペレータのユニークなID');
            $table->foreignId('site_id')->constrained()->comment('関連するサイトID');
            $table->foreignId('operator_id')->constrained()->comment('関連するオペレータID');
            $table->foreignId('role_id')->constrained('site_operator_roles')->comment('サイトオペレータの役割ID');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');

            $table->unique(['site_id', 'operator_id', 'role_id']);
        });
    }

    /**
     * マイグレーションをロールバック
     */
    public function down(): void
    {
        Schema::dropIfExists('site_operators');
        Schema::dropIfExists('operators');
        Schema::dropIfExists('operator_ranks');
        Schema::dropIfExists('site_operator_roles');
    }
};
