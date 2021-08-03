<?php

use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class CreateHeroesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('heroes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('hero_role_id')->comment('英雄定位ID');
            $table->string('name')->comment('名称');
            $table->string('avatar')->comment('头像');
            $table->string('image')->nullable()->comment('大图');
            $table->json('additional')->nullable()->comment('额外信息');
            $table->timestamps();
            $table->comment('英雄表');
        });
    }

    /**
     * Reverse the migrations.
     *
     */
    public function down(): void
    {
        Schema::dropIfExists('heroes');
    }
}
