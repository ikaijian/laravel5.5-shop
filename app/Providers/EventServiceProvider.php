<?php

namespace App\Providers;

use App\Events\OrderPaid;
use App\Listeners\RegisteredListener;
use App\Listeners\SendOrderPaidMail;
use App\Listeners\UpdateProductSoldCount;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
        //注册监听器
        Registered::class => [
            RegisteredListener::class,
        ],

        //注册支付监听
        OrderPaid::class=>[
            UpdateProductSoldCount::class,
            //注册邮件监听
            SendOrderPaidMail::class,
        ],



    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
