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

###订单模块
>订单是电商系统的核心之一，本章节将要实现把购物车中的商品提交成订单

由于一笔订单支持多个商品 SKU，因此需要 orders 和 order_items 两张
####orders 表
orders表

| 字段名称  | 描述 | 类型    | 加索引缘由  |
|-------|:---:|-----------|-------:|
|id	            |自增长 ID	            |unsigned int	|主键
|no	            |订单流水号	            |varchar	    |唯一
|user_id	    |下单的用户 ID	        |unsigned int	|外键
|address	    |JSON 格式的收货地址	|text	        |无
|total_amount	|订单总金额	            |decimal	    |    无
|remark	        |订单备注	            |text, null	    |无
|paid_at	    |支付时间	            |datetime, null	|无
|payment_method	|支付方式	            |varchar, null	|无
|payment_no	    |支付平台订单号	        |varchar, null	|无
|refund_status	|退款状态	            |varchar	        |无
|refund_no	    |退款单号	            |varchar, null	    |唯一
|closed	        |订单是否已关闭	        |tinyint, default 0	|无
|reviewed	    |订单是否已评价	        |tinyint, default 0	|无
|ship_status	|物流状态	            |varchar	             |无
|ship_data	    |物流数据	            |text, null	         |无
|extra	        |其他额外的数据	        |text, null	         |无

把收货地址用 JSON 格式保存而不是直接用一个外键连接到地址表，
假如用户用地址 A 创建了一个订单，然后又修改了地址 A，
那么用外键连接的方式这个订单的地址就会变成新地址，这并不符合正常的逻辑，
所以需要用 JSON 格式把下单时的地址快照进订单，这样无论用户是修改还是删除之前的地址，
都不会影响到之前的订单

~~~php
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{

    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id')->comment("自增长 ID");
            $table->string('no')->unique()->comment("订单流水号");
            $table->unsignedInteger('user_id')->comment("下单的用户 ID");
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('address')->comment("JSON 格式的收货地址");
            $table->decimal('total_amount', 10, 2)->comment("订单总金额");
            $table->text('remark')->nullable()->comment("订单备注");
            $table->dateTime('paid_at')->nullable()->comment("支付时间");
            $table->string('payment_method')->nullable()->comment("支付方式");
            $table->string('payment_no')->nullable()->comment("支付平台订单号");
            $table->string('refund_status')->comment("退款状态");
            $table->string('refund_no')->unique()->nullable()->comment("退款单号");
            $table->boolean('closed')->default(false)->comment("订单是否已关闭");
            $table->boolean('reviewed')->default(false)->comment("订单是否已评价");
            $table->string('ship_status')->comment("物流状态");
            $table->text('ship_data')->nullable()->comment("物流数据");
            $table->text('extra')->nullable()->comment("其他额外的数据");
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}

~~~
####order_items 表
order_items表

| 字段名称  | 描述 | 类型    | 加索引缘由  |
|-------|:---:|-----------|-------:|
|id	        |自增长 ID	    |unsigned int	|主键
|order_id	|所属订单 ID	|unsigned int	|外键
|product_id	|对应商品 ID	|unsigned int	|外键
|product_sku_id	|对应商品 SKU ID	|unsigned int	|外键
|amount	|数量	    |unsigned int	            |无
|price	|单价	    |decimal	                |无
|rating	|用户打分	|unsigned int, null	        |无
|review	|用户评价	|text, null	                |无
|reviewed_at	|评价时间	|timestamp, null	|无

创建模型
~~~
php artisan make:model Models/Order -mf
php artisan make:model Models/OrderItem -mf
~~~