<?php
/**
 * Created by PhpStorm.
 * User: 陈开坚(jianjian)
 * Date: 2019/8/7
 * Time: 0:19
 */

namespace App\Services;


use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;
use App\Models\Order;
use App\Models\ProductSku;
use App\Models\User;
use App\Models\UserAddress;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderService
{

    public function store(User $user, UserAddress $address, $remark, $items)
    {

        //开启事务
        $order = DB::transaction(function () use ($user, $address, $remark, $items) {
            // 更新此地址的最后使用时间
            $address->update(['last_used_at' => Carbon::now()]);
            // 创建一个订单
            $order = new Order([
                'address' => [// 将地址信息放入订单中
                    'address' => $address->full_address,
                    'zip' => $address->zip,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark' => $remark,
                'total_amount' => 0,
            ]);

            $order->user()->associate($user);
            $order->save();

            $totalAmount = 0;
            // 遍历用户提交的 SKU

            foreach ($items as $datas) {

                $sku = ProductSku::find($datas['sku_id']);
                // 创建一个 OrderItem 并直接与当前订单关联

                $items = $order->items()->make([
                    'amount' => $datas['amount'],
                    'price' => $sku->price,
                ]);

                $items->product()->associate($sku->product_id);
                $items->productSku()->associate($sku);
                $items->save();

                $totalAmount += $sku->price * $datas['amount'];

                if ($sku->decreaseStock($datas['amount']) <= 0) {
                    throw  new InvalidRequestException('该商品库存不足');
                }

                // 更新订单总金额
                $order->update(['total_amount' => $totalAmount]);

                // 将下单的商品从购物车中移除
                $skuIds = collect($items)->pluck('sku_id')->all();
                app(CartService::class)->remove($skuIds);

                return $order;
            }
        });

        // 这里我们直接使用 dispatch 函数
        dispatch(new CloseOrder($order,config('app.order_ttl')));

        return $order;
    }
}