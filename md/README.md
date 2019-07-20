##用户模块
- 通过数据库迁移为user表添加一个字段email_verified用来做邮箱校验
~~~
php artisan make:migration users_add_email_verified --table=users
##生成一个迁移文件database/migrations/2019_07_20_075809_users_add_email_verified.php
~~~
~~~php
<?php 

 use Illuminate\Support\Facades\Schema;
 use Illuminate\Database\Schema\Blueprint;
 use Illuminate\Database\Migrations\Migration;
 
 /**
 * 说明：boolean('email_verified') 代表添加一个名为 email_verified 的布尔类型字段（在 Mysql 中是 tinyint(1) 类型）
 *   default(false) 代表默认值是 false（在 Mysql 中是 0）
  *  after('remember_token') 代表字段的位置是在 remember_token 后面
 */
 class UsersAddEmailVerified extends Migration
 {
    
     public function up()
     {
         Schema::table('users', function (Blueprint $table) {
             //
             $table->boolean('email_verified')->default(false)->after('remember_token');
         });
     }
     
     public function down()
     {
         Schema::table('users', function (Blueprint $table) {
             //
             $table->dropColumn('email_verified');
         });
     }
 }
~~~
运行
~~~
php artisan migrate
//为了检查回滚代码有没有问题,执行一次回滚
php artisan migrate:rollback
php artisan migrate
~~~

- 模型中$casts属性
>$casts 属性提供了一个便利的方法来将数据库字段值转换为常见的数据类型，$casts 属性应是一个数组，
且数组的键是那些需要被转换的字段名，值则是你希望转换的数据类型。
支持转换的数据类型有： integer，real，float，double，string，boolean，object，array，collection，date，datetime 和 timestamp
~~~
 //$casts 属性,这个字段要转换成 bool 类型
    protected $casts = [
        'email_verified' => 'boolean',
    ];
~~~

- 中间件
>校验是否已经有权限；
希望用户在验证邮箱之后才能正常使用系统的功能，
当用户尚未验证邮箱时，访问其他页面都会被重定向到一个提示验证邮箱的页面。
对于这种需求我们可以通过中间件来解决，把需要验证邮箱的路由放到拥有这个中间件的路由组中，
当用户访问这些路由时会先执行中间件检查是否验证了邮箱

创建中间件
~~~
//运行会生成中间件文件Http/Middleware/CheckIfEmailVerified.php
php artisan make:middleware CheckIfEmailVerified
~~~
当中间件被执行时，Laravel 会调用中间件的 handle 方法，
第一个参数是当前请求对象，第二个参数是执行下一个中间件的闭包函数;
$next($request) 代表执行下一个中间件
~~~

public function handle($request, Closure $next)
    {
        
        return $next($request);
    }
~~~

再在 Kernel.php注册刚刚创建的中间件
~~~
protected $routeMiddleware = [
        'email_verified' => \App\Http\Middleware\CheckIfEmailVerified::class,
    ];
~~~


##用户模块
##商品模块
##订单模块
##支付模块
##优惠券模块
##管理模块