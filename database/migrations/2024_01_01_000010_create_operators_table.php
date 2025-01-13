<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operators', function (Blueprint $table) {
            $table->id();
            $table->string('operator_code', 64)->unique()->comment('オペレータコード');
            $table->foreignId('company_id')->constrained()->comment('所属する会社ID');
            $table->string('name', 32)->comment('オペレータ名');
            $table->foreignId('operator_rank_id')->constrained()->comment('オペレータランクID');
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
            $table->index('operator_rank_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operators');
    }
}; 