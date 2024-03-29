<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

// 代表这个类需要被放到队列中执行，而不是触发时立即执行
class CloseOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order, $delay)
    {
        //
        $this->order = $order;
        // 设置延迟的时间，delay() 方法的参数代表多少秒之后执行
        $this->delay($delay);
    }

    /**
     * // 定义这个任务类具体的执行逻辑
     * // 当队列处理器从队列中取出任务时，会调用 handle() 方法
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        if ($this->order->paid_at) {
            return;
        }

        DB::transaction(function (){
            if ($this->order->update(['closed'=>true])) {
                foreach ($this->order->items as $item){
                    $item->productSku->addStock($item->amount);
                }
            }
        });
    }
}
