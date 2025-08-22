<?php

declare(strict_types=1);

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateProductTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('商品ID');
            $table->string('name', 200)->comment('商品名称');
            $table->string('sn', 100)->unique()->comment('商品编号');
            $table->text('description')->nullable()->comment('商品描述');
            $table->text('content')->nullable()->comment('商品详情');
            $table->string('image')->nullable()->comment('商品主图');
            $table->json('images')->nullable()->comment('商品图片集');
            $table->bigInteger('category_id')->nullable()->comment('分类ID');
            $table->decimal('price', 10, 2)->default(0)->comment('商品价格');
            $table->decimal('original_price', 10, 2)->nullable()->comment('原价');
            $table->integer('stock')->default(0)->comment('库存数量');
            $table->integer('sales')->default(0)->comment('销量');
            $table->tinyInteger('status')->default(1)->comment('状态：1-下架，2-上架，3-售罄');
            $table->integer('sort')->default(0)->comment('排序');
            $table->decimal('weight', 8, 2)->nullable()->comment('重量(kg)');
            $table->string('unit', 20)->default('件')->comment('单位');
            $table->json('specs')->nullable()->comment('商品规格');
            $table->json('attributes')->nullable()->comment('商品属性');
            $table->tinyInteger('is_virtual')->default(0)->comment('是否虚拟商品：0-否，1-是');
            $table->tinyInteger('is_hot')->default(0)->comment('是否热门：0-否，1-是');
            $table->tinyInteger('is_recommend')->default(0)->comment('是否推荐：0-否，1-是');
            $table->timestamp('shelf_time')->nullable()->comment('上架时间');
            $table->bigInteger('created_by')->comment('创建者');
            $table->bigInteger('updated_by')->nullable()->comment('更新者');
            $table->timestamps();
            $table->string('remark')->nullable()->comment('备注');
            
            $table->index(['status', 'sort']);
            $table->index('category_id');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product');
    }
}

