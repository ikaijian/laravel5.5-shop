<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Monolog\Logger;
use Yansongda\Pay\Pay;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        //sql调试：打印sql语句

//        \DB::listen(
//            function ($sql) {
//                foreach ($sql->bindings as $i => $binding) {
//                    if ($binding instanceof \DateTime) {
//                        $sql->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
//                    } else {
//                        if (is_string($binding)) {
//                            $sql->bindings[$i] = "'$binding'";
//                        }
//                    }
//                }
//
//                // Insert bindings into query
//                $query = str_replace(array('%', '?'), array('%%', '%s'), $sql->sql);
//
//                $query = vsprintf($query, $sql->bindings);
//
//                // Save the query to file
//                $logFile = fopen(
//                    storage_path('logs' . DIRECTORY_SEPARATOR . date('Y-m-d') . '_query.log'),
//                    'a+'
//                );
//                fwrite($logFile, date('Y-m-d H:i:s') . ': ' . $query . PHP_EOL);
//                fclose($logFile);
//            }
//        );
        //
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        // 往服务容器中注入一个名为 alipay 的单例对象
        /**
         *
         * $this->app->singleton() 往服务容器中注入一个单例对象，
         * 第一次从容器中取对象时会调用回调函数来生成对应的对象并保存到容器中，之后再去取的时候直接将容器中的对象返回
         */
        $this->app->singleton('alipay',function (){
            $config = config('pay.alipay');
            //回调
            $config['notify_url'] = route('payment.alipay.notify');
            $config['return_url'] = route('payment.alipay.return');
            // 判断当前项目运行环境是否为线上环境
            //app()->environment() 获取当前运行的环境，线上环境会返回 production
            if (app()->environment() !== 'production') {
                $config['mode'] = 'dev';
                $config['log']['level'] = Logger::DEBUG;
            }else{
                $config['log']['level'] = Logger::DEBUG;
            }
            // 调用 Yansongda\Pay 来创建一个支付宝支付对象
            return Pay::alipay($config);
        });
        $this->app->singleton('wechat_pay', function () {
            $config = config('pay.wechat');
            $config['notify_url'] =route('payment.wechat.notify');
            if (app()->environment() !== 'production') {
                $config['log']['level'] = Logger::DEBUG;
            } else {
                $config['log']['level'] = Logger::WARNING;
            }
            // 调用 Yansongda\Pay 来创建一个微信支付对象
            return Pay::wechat($config);
        });
    }
}
