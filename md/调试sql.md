 ##laravel调试sql语句
 $builder = Product::query()->where('on_sale', true);      
dd($builder->toSql());

~~~mysql
select * from `products` 
where `on_sale` = ?
 and (`title` like ? or `description` like ? 
      or exists (
          select * from `product_skus`
           where `products`.`id` = `product_skus`.`product_id` 
           and (`title` like ? or `description` like ?)
      )
 )
~~~

~~~mysql
select * from `products` 
where `on_sale` = ? 
and `title` like ? or `description` like ? 
or exists (
    select * from `product_skus` 
    where `products`.`id` = `product_skus`.`product_id` 
    and (`title` like ? or `description` like ?)
    )
~~~


~~~
public function verified()
    {
        $users = User::where('email_verified','0')->get();
        \DB::enableQueryLog();
        foreach ($users as $user) {
            \App\Jobs\EmailVerified::dispatch($user);
        }
        dump(\DB::getQueryLog());
        return 'finish';
    }
~~~

###第二种
app\providers\AppServiceProvider文件下配置
~~~php
<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
        //
    }
}

~~~