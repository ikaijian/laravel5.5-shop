@extends('layouts.app')
@section('title', '查看订单')

@section('content')
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>订单详情</h4>
                </div>
                <div class="panel-body">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>商品信息</th>
                            <th class="text-center">单价</th>
                            <th class="text-center">数量</th>
                            <th class="text-right item-amount">小计</th>
                        </tr>
                        </thead>
                        @foreach($order->items as $index => $item)
                            <tr>
                                <td class="product-info">
                                    <div class="preview">
                                        <a target="_blank" href="{{ route('products.show', [$item->product_id]) }}">
                                            <img src="{{ $item->product->image_url }}">
                                        </a>
                                    </div>
                                    <div>
            <span class="product-title">
               <a target="_blank"
                  href="{{ route('products.show', [$item->product_id]) }}">{{ $item->product->title }}</a>
             </span>
                                        <span class="sku-title">{{ $item->productSku->title }}</span>
                                    </div>
                                </td>
                                <td class="sku-price text-center vertical-middle">￥{{ $item->price }}</td>
                                <td class="sku-amount text-center vertical-middle">{{ $item->amount }}</td>
                                <td class="item-amount text-right vertical-middle">
                                    ￥{{ number_format($item->price * $item->amount, 2, '.', '') }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="4"></td>
                        </tr>
                    </table>
                    <div class="order-bottom">
                        <div class="order-info">
                            <div class="line">
                                <div class="line-label">收货地址：</div>
                                <div class="line-value">{{ join(' ', $order->address) }}</div>
                            </div>
                            <div class="line">
                                <div class="line-label">订单备注：</div>
                                <div class="line-value">{{ $order->remark ?: '-' }}</div>
                            </div>
                            <div class="line">
                                <div class="line-label">订单编号：</div>
                                <div class="line-value">{{ $order->no }}</div>
                            </div>

                            <!-- 输出物流状态 -->
                            <div class="line">
                                <div class="line-label">物流状态：</div>
                                <div class="line-value">{{ \App\Models\Order::$shipStatusMap[$order->ship_status] }}</div>
                            </div>
                            <!-- 如果有物流信息则展示 -->
                            @if($order->ship_data)
                                <div class="line">
                                    <div class="line-label">物流信息：</div>
                                    <div class="line-value">{{ $order->ship_data['express_company'] }} {{ $order->ship_data['express_no'] }}</div>
                                </div>
                            @endif

                        </div>


                        <div class="order-summary text-right">
                            <div class="total-amount">
                                <span>订单总价：</span>
                                <div class="value">￥{{ $order->total_amount }}</div>
                            </div>
                            <div>
                                <span>订单状态：</span>
                                <div class="value">
                                    @if($order->paid_at)
                                        @if($order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
                                            已支付
                                        @else
                                            {{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}
                                        @endif
                                    @elseif($order->closed)
                                        已关闭
                                    @else
                                        未支付
                                    @endif
                                </div>

                                <!-- 支付按钮开始 -->
                                @if(!$order->paid_at && !$order->closed)
                                    <div class="payment-buttons">
                                        <a class="btn btn-primary btn-sm"
                                           href="{{ route('payment.alipay', ['order' => $order->id]) }}">支付宝支付</a>
                                        <a class="btn btn-success btn-sm"
                                           href="{{ route('payment.wechat', ['order' => $order->id]) }}">微信支付</a>
                                        <!-- 把之前的微信支付按钮换成这个 -->
                                        {{--<button class="btn btn-sm btn-success" id='btn-wechat'>微信支付</button>--}}
                                    </div>
                                @endif
                            <!-- 支付按钮结束 -->

                                <!-- 如果订单的发货状态为已发货则展示确认收货按钮 -->
                                @if($order->ship_status === \App\Models\Order::SHIP_STATUS_DELIVERED)
                                    <div class="receive-button">
                                        <form method="post" action="{{ route('orders.received', [$order->id]) }}">
                                            <!-- csrf token 不能忘 -->
                                        {{ csrf_field() }}
                                        {{--<button type="submit" class="btn btn-sm btn-success">确认收货</button>--}}
                                        <!-- 将原本的表单替换成下面这个按钮 -->
                                            <button type="button" id="btn-receive" class="btn btn-sm btn-success">确认收货
                                            </button>
                                        </form>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scriptsAfterJs')
    <script>
        $(document).ready(function () {
            // 微信支付按钮事件
            $('#btn-wechat').click(function () {
                swal({
                    // content 参数可以是一个 DOM 元素，这里我们用 jQuery 动态生成一个 img 标签，并通过 [0] 的方式获取到 DOM 元素
                    content: $('<img src="{{ route('payment.wechat', ['order' => $order->id]) }}" />')[0],
                    // buttons 参数可以设置按钮显示的文案
                    buttons: ['关闭', '已完成付款'],
                })
                    .then(function (result) {
                        // 如果用户点击了 已完成付款 按钮，则重新加载页面
                        if (result) {
                            location.reload();
                        }
                    })
            });

            // 确认收货按钮点击事件
            $('#btn-receive').click(function () {
                // 弹出确认框
                swal({
                    title: "确认已经收到商品？",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                    buttons: ['取消', '确认收到'],
                })
                    .then(function (ret) {
                        // 如果点击取消按钮则不做任何操作
                        if (!ret) {
                            return;
                        }
                        // ajax 提交确认操作
                        axios.post('{{ route('orders.received', [$order->id]) }}')
                            .then(function () {
                                // 刷新页面
                                location.reload();
                            })
                    });
            });
        });
    </script>
@endsection