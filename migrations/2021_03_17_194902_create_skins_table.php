<?php

use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class CreateSkinsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('skins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('hero_id')->comment('英雄id');
            $table->string('name')->comment('名称');
            $table->string('avatar')->comment('头像');
            $table->string('image')->comment('图像');
            $table->string('cropped')->comment('裁切后的图片');
            $table->json('additional')->nullable()->comment('额外信息');
            $table->unsignedBigInteger('price')->comment('价格');
            $table->unsignedBigInteger('fragments')->comment('碎片');
            $table->json('deploy')->nullable()->comment('配置');
            $table->unsignedSmallInteger('status')->default(0)->comment('状态');
            $table->unsignedBigInteger('admin_id')->nullable()->comment('管理员id');
            $table->timestamp('expired_at')->nullable()->comment('失效时间');
            $table->timestamps();
            $table->comment('英雄皮肤表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skins');
    }
}
