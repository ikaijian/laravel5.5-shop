##用户模块

###修改表结构
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
###模型中一些属性说明
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
###中间件
- 中间件
>校验是否已经有权限；
希望用户在验证邮箱之后才能正常使用系统的功能，
当用户尚未验证邮箱时，访问其他页面都会被重定向到一个提示验证邮箱的页面。
对于这种需求我们可以通过中间件来解决，把需要验证邮箱的路由放到拥有这个中间件的路由组中，
当用户访问这些路由时会先执行中间件检查是否验证了邮箱

1创建中间件
~~~
//运行会生成中间件文件Http/Middleware/CheckIfEmailVerified.php
php artisan make:middleware CheckIfEmailVerified
~~~
2：修改中间 handel 方法  
当中间件被执行时，Laravel 会调用中间件的 handle 方法，
第一个参数是当前请求对象，第二个参数是执行下一个中间件的闭包函数;
$next($request) 代表执行下一个中间件
~~~

 public function handle($request, Closure $next)
    {
        if (!$request->user()->email_verified) {
            // 如果是 AJAX 请求，则通过 JSON 返回
            if ($request->expectsJson()) {
                return response()->json(['msg' => '请先验证邮箱'], 400);
            }
            return redirect(route('email_verify_notice'));
        }
        return $next($request);
    }
~~~

3.注册中间件  
再在 Kernel.php注册刚刚创建的中间件
~~~
protected $routeMiddleware = [
        'email_verified' => \App\Http\Middleware\CheckIfEmailVerified::class,
    ];
~~~

###通知类
- 创建验证邮件通知类
>通过 Laravel 内置的通知模块（Notification）来实现验证邮件的发送
~~~
//生成的通知类放置在 app/Notifications 目录下，生成的通知类：EmailVerificationNotification.php
php artisan make:notification EmailVerificationNotification
~~~

~~~php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class EmailVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }
    // 只需要通过邮件通知，因此这里只需要一个 mail 即可
    public function via($notifiable)
    {
        return ['mail'];
    }
    
   // 发送邮件时会调用此方法来构建邮件内容，参数就是 App\Models\User 对象
    public function toMail($notifiable)
    {
         return (new MailMessage)
                            ->greeting($notifiable->name.'您好：')
                            ->subject('注册成功，请验证您的邮箱')
                            ->line('请点击下方链接验证您的邮箱')
                            ->action('验证', url('/'));
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

~~~
~~~
代码解析：

在类的申明里加上了 implements ShouldQueue，ShouldQueue 这个接口本身没有定义任何方法，
对于实现了 ShouldQueue 的邮件类 Laravel 会用将发邮件的操作放进队列里来实现异步发送；
greeting() 方法可以设置邮件的欢迎词；
subject() 方法用来设定邮件的标题；
line() 方法会在邮件内容里添加一行文字；
action() 方法会在邮件内容里添加一个链接按钮。这里就是激活链接，暂时把链接设成了主页，接下来我们来实现这个激活链接的逻辑。
~~~
- 激活链接
>当发送注册激活邮件时，我们会生成一个随机字符串，然后以邮箱为 Key、随机字符串作为值保存在缓存中，
邮箱和这个随机字符串会作为激活链接的参数。当用户点击激活链接时，
只需要从缓存中取出对应的数据并判断是否一致就可以确定这个激活链接是否正确


创建一个控制器 EmailVerificationController
~~~
php artisan make:controller EmailVerificationController
~~~

###监听器
>监听器是 Laravel 事件系统的重要组成部分，当一个事件被触发时，对应的监听器就会被执行，
可以很方便地解耦代码。还可以把监听器配置成异步执行，比较适合一些不需要获得返回值并且耗时较长的任务，
比如本章节的发送邮件。

- 注册时触发发送激活邮件(事件的监听器)
>希望用户在注册完成之后系统就会发送激活邮件，而不是让用户自己去请求激活邮件;
用户注册完成之后会触发一个 Illuminate\Auth\Events\Registered 事件

- 创建一个事件的监听器
~~~
//生成的监听器文件在 app/Listeners 目录下：app/Listeners/RegisteredListener.php

php artisan make:listener RegisteredListener
~~~

~~~php
<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

// implements ShouldQueue 让这个监听器异步执行
class RegisteredListener implements ShouldQueue
{
    
    public function __construct()
    {
        //
    }

// 当事件被触发时，对应该事件的监听器的 handle() 方法就会被调用
    public function handle($event)
    {
        //
    }
}

~~~
- 监听器创建完成之后还需要在 EventServiceProvider 中将事件和监听器关联起来才能生效
app/Providers/EventServiceProvider.php

~~~php
<?php
namespace App\Providers;

use App\Listeners\RegisteredListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
        //注册监听器
        Registered::class => [
            RegisteredListener::class,
        ],
    ];


    public function boot()
    {
        parent::boot();

        //
    }
}

~~~

###异常
>异常指的是在程序运行过程中发生的异常事件，通常是由外部问题所导致的

+ 生成异常类
~~~
//新创建的异常文件保存在 app/Exceptions/ 目录下：app/Exceptions/InvalidRequestException.php

php artisan make:exception InvalidRequestException
~~~
自己定义异常类
~~~php
<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;


class InvalidRequestException extends Exception
{
    //
    public function __construct(string $message= "",int $code = 400)
    {
        parent::__construct($message,$code);

    }

    //Laravel 5.5 之后支持在异常类中定义 render() 方法，该异常被触发时系统会调用 render() 方法来输出
    public function render( Request $request)
    {
        if ($request->expectsJson()) {

            // json() 方法第二个参数就是 Http 返回码
            return response()->json(['msg'=>$this->message],$this->code);
        }
        return view('pages.error', ['msg' => $this->message]);
    }
}

~~~
当异常触发时 Laravel 默认会把异常的信息和调用栈打印到日志里;
而此类异常并不是因为我们系统本身的问题导致的，不会影响我们系统的运行，
如果大量此类日志打印到日志文件里反而会影响我们去分析真正有问题的异常，因此需要屏蔽这个行为。
Laravel 内置了屏蔽指定异常写日志的解决方案：
app/Exceptions/Handler.php
~~~
protected $dontReport = [
        InvalidRequestException::class,
    ];
~~~
当一个异常被触发时，Laravel 会去检查这个异常的类型是否在 $dontReport 属性中定义了，
如果有则不会打印到日志文件中

##用户模块
##商品模块
##订单模块
##支付模块
##优惠券模块
##管理模块