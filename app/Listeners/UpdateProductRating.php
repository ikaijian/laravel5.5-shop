<?php

namespace App\Listeners;

use App\Events\OrderReviewed;
use App\Models\OrderItem;
use function foo\func;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

// implements ShouldQueue 代表这个事件处理器是异步的
class UpdateProductRating implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderReviewed  $event
     * @return void
     */
    public function handle(OrderReviewed $event)
    {
        // 通过 with 方法提前加载数据，避免 N + 1 性能问题
        $items = $event->getOrder()->items()->with(['product'])->get();

        foreach ($items as $item) {
            $result  = OrderItem::query()
                ->where('product_id',$item->product_id)
                ->whereHas('order',function ($query){
                   $query->whereNotNull('paid_at');
                })
//                first() 方法接受一个数组作为参数，代表此次 SQL 要查询出来的字段
                ->first([
//                    构建 SQL 的时候如果遇到 DB::raw() 就会把 DB::raw() 的参数原样拼接到 SQL 里
                    DB::raw('count(*) as review_count'),
                    DB::raw('avg(rating) as rating'),
                ]);

            // 更新商品的评分和评价数
            $item->product->update([
                'rating'       => $result->rating,
                'review_count' => $result->review_count,
            ]);
        }

    }
}
