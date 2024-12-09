<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('line_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('sites');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('line_user_id')->unique();
            $table->string('nonce')->nullable();
            $table->string('display_name');
            $table->string('picture_url')->nullable();
            $table->string('status_message')->nullable();
            $table->boolean('is_linked')->default(false);
            $table->timestamp('followed_at')->nullable();
            $table->timestamp('unfollowed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['site_id', 'line_user_id']);
            $table->index(['site_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('line_users');
    }
};
