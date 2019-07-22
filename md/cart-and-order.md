##购物车模块和订单模块
>购物车的数据通常会保存到 Session 或者数据库,对于拥有多个端（网页、App）的电商网站来说
为了保障用户体验会使用数据库来保存购物车中的数据，这样用户在网页端加入购物车的商品也能在 App 中看到。
本项目虽然目前只有一个网页端，但这是一个实战项目，是有可能拓展出 APP、小程序等其他端

###购物车表设计
cart_items表

| 字段名称  | 描述 | 类型    | 加索引缘由  |
|-------|:---:|-----------|-------:|
|id	            |自增长 ID	    |unsigned int	|主键
|user_id	    |所属用户 ID	|unsigned int	|外键
|product_sku_id	|商品 SKU ID	|unsigned int	|外键
|amount	        |商品数量	    |unsigned int	|无

~~~
php artisan make:model Models/CartItem -m
~~~

~~~php
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->increments('id')->comment('自增长ID');
            $table->unsignedInteger('user_id')->comment('所属用户ID');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('product_sku_id')->comment('商品SKU_ID');
            $table->foreign('product_sku_id')->references('id')->on('product_skus')->onDelete('cascade');
            $table->unsignedInteger('amount')->comment('商品数量');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_items');
    }
}

~~~

迁移
~~~
php artisan migrate
~~~

