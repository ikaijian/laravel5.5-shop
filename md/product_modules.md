##商品模块

###数据库表设计
+ products 表，产品信息表，对应数据模型 Product ；
+ product_skus 表，产品的 SKU 表，对应数据模型 ProductSku

####products 表
| 字段名称  | 描述 | 类型    | 加索引缘由  |
|-------|:---:|-----------|-------:|
|id	            | 自增长 ID	            | unsigned int	            | 主键
|title	        | 商品名称	            | varchar	                | 无
|description	| 商品详情	            | text	                    | 无
|image	        | 商品封面图片文件路径	| varchar	                | 无
|on_sale	    | 商品是否正在售卖	    | tiny int, default 1	    | 无
|rating	        | 商品平均评分	        | float, default 5	        | 无
|sold_count	    | 销量	                | unsigned int, default 0	| 无
|review_count	| 评价数量	            | unsigned int, default 0	| 无
|price	        | SKU 最低价格	        | decimal	                | 无

~~~php
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id')->comment('自增长 ID');
            $table->string('title')->comment('商品名称');
            $table->text('description')->comment('商品详情');
            $table->string('image')->comment('商品封面图片文件路径');
            $table->boolean('on_sale')->default(true)->comment('商品是否正在售卖');
            $table->float('rating')->default(5)->comment('商品平均评分');
            $table->unsignedInteger('sold_count')->default(0)->comment('销量');
            $table->unsignedInteger('review_count')->default(0)->comment('评价数量');
            $table->decimal('price', 10, 2)->comment('SKU 最低价格');;
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}

~~~
####product_skus 表
| 字段名称  | 描述 | 类型    | 加索引缘由  |
|-------|:---:|-----------|-------:|
|id	            | 自增长 ID	| unsigned int	| 主键
|title	        | SKU 名称	| varchar	        |无
|description	| SKU 描述	| varchar	        |无
|price	        | SKU 价格	| decimal	        |无
|stock	        | 库存	    | unsigne int	    |无
|product_id	    | 所属商品    | id	unsigne int	|外键


~~~php
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductSkusTable extends Migration
{
   
    public function up()
    {
        Schema::create('product_skus', function (Blueprint $table) {
            $table->increments('id')->comment('自增长 ID');
            $table->string('title')->comment('SKU 名称');
            $table->string('description')->comment('SKU 描述');
            $table->decimal('price', 10, 2)->comment('SKU 价格');
            $table->unsignedInteger('stock')->comment('库存');
            $table->unsignedInteger('product_id')->comment('所属商品');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_skus');
    }
}

~~~
###创建模型
~~~
php artisan make:model Models/Product -mf
php artisan make:model Models/ProductSku -mf
~~~