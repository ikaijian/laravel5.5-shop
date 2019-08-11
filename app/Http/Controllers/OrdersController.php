<?php

namespace App\Http\Controllers;

use App\Events\OrderReviewed;
use App\Exceptions\InternalException;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\SendReviewRequest;
use App\Jobs\AutoReceive;
use App\Jobs\CloseOrder;
use App\Models\Order;
use App\Models\ProductSku;
use App\Models\UserAddress;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{


    /**
     * 用户订单列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $orders = Order::query()->with(['items.product', 'items.productSku'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate();

        return view('orders.index', ['orders' => $orders]);
    }

    /**
     * 下订单
     * @param OrderRequest $request
     * @return mixed
     */
    // 利用 Laravel 的自动解析功能注入 CartService 类
//    public function store(OrderRequest $request,OrderService $orderService)
//    {
//
//        $user = $request->user();
//
//        //开启数据库事务
//        $order = DB::transaction(function () use ($user, $request, $cartService) {
//            $address = UserAddress::find($request->input('address_id'));
//            // 更新此地址的最后使用时间
//            $address->update(['last_used_at' => Carbon::now()]);
//            // 创建一个订单
//            $order = new Order([
//                'address' => [
//                    'address' => $address->full_address,
//                    'zip' => $address->zip,
//                    'contact_name' => $address->contact_name,
//                    'contact_phone' => $address->contact_phone,
//                ],
//                'remark' => $request->input('remark'),
//                'total_amount' => 0,
//            ]);
//
//            // 订单关联到当前用户
//            $order->user()->associate($user);
//            // 写入数据库
//            $order->save();
//
//            $totalAmount = 0;
//            $items = $request->input('items');
//
//            // 遍历用户提交的 SKU
//            foreach ($items as $data) {
//                $sku = ProductSku::find($data['sku_id']);
//                // 创建一个 OrderItem 并直接与当前订单关联
//                //$order->items()->make() 方法可以新建一个关联关系的对象（也就是 OrderItem）但不保存到数据库，
//                //这个方法等同于 $item = new OrderItem(); $item->order()->associate($order);
//                $items = $order->items()->make([
//                    'amount' => $data['amount'],
//                    'price' => $sku->price,
//                ]);
//
//                $items->product()->associate($sku->product_id);
//                $items->productSku()->associate($sku);
//                $items->save();
//                $totalAmount += $sku->price * $data['amount'];
//
//                if ($sku->decreaseStock($data['amount']) <= 0) {
//                    throw new InternalException('该商品库存不足');
//                }
//
//            }
//
//            // 更新订单总金额
//            $order->update(['total_amount' => $totalAmount]);
////            // 将下单的商品从购物车中移除
////            $skuIds = collect($items)->pluck('sku_id');
////            $user->cartItems()->whereIn('product_sku_id', $skuIds)->delete();
//
//            //将下单的商品从购物车中移除
//            //封装之后代码
//            $skuIds = collect($request->input('items'))->pluck('sku_id')->all();
//            $cartService->remove($skuIds);
//
//            return $order;
//        });
//        //队列定时关闭未支付订单,Job
//        $this->dispatch(new CloseOrder($order, config('app.order_ttl')));
//        return $order;
//    }

    //封装之后
    public function store(OrderRequest $request, OrderService $orderService)
    {
        $user = $request->user();

        $address = UserAddress::find($request->input('address_id'));

        return $orderService->store($user, $address, $request->input('remark'), $request->input('items'));
    }


    /**
     * 订单详情
     * load() 方法与 with() 预加载方法有些类似，称为 延迟预加载，不同点在于 load() 是在已经查询出来的模型上调用，而 with() 则是在 ORM 查询构造器上调用
     * @param Order $order
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Order $order, Request $request)
    {
        //订单策略使用
        $this->authorize('own', $order);

        return view('orders.show', ['order' => $order->load(['items.productSku', 'items.product'])]);
    }

    //确认收货
    public function received(Order $order, Request $request)
    {
        // 校验权限
        $this->authorize('own', $order);
        // 判断订单的发货状态是否为已发货
        if ($order->ship_status !== Order::SHIP_STATUS_DELIVERED) {
            throw new InvalidRequestException('发货状态不正确');
        }
        // 更新发货状态为已收到
        $order->update(['ship_status' => Order::SHIP_STATUS_RECEIVED]);

        // 返回原页面
//        return redirect()->back();
        //调用自动确认收货队列
        $this->dispatch( new AutoReceive($order,config('app.auto_receive_ttl')));

        // 返回订单信息
        return $order;
    }

    //评价
    public function review(Order $order)
    {
        //校验权限
        $this->authorize('own',$order);

        //判断是否已经支付
        if (!$order->paid_at) {
            throw new  InvalidRequestException('该订单未支付，不可评价');
        }

        //使用 load 方法加载关联数据，避免 N + 1 性能问题

        return view('orders.review',['order'=>$order->load(['items.productSku','items.product'])]);

    }

    //保存评论
    public function sendReview(Order $order,SendReviewRequest $request)
    {
        //校验权限
        $this->authorize('own',$order);
        //判断是否已经支付
        if (!$order->paid_at) {
            throw new  InvalidRequestException('该订单未支付，不可评价');
        }

        $reviews = $request->input('reviews');

        DB::transaction(function () use($reviews,$order){
            foreach ($reviews as $review){
                $orderItem = $order->items()->find($review['id']);
                // 保存评分和评价
                $orderItem->update([
                   'rating'=>$review['rating'],
                   'review'=>$review['review'],
                   'reviewed_at'=>Carbon::now(),
                ]);
            }
            // 将订单标记为已评价
            $order->update(['reviewed' => true]);
            event(new OrderReviewed($order));
        });
        return redirect()->back();
    }
}
