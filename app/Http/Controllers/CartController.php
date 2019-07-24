<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;
use App\Models\ProductSku;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * 添加购物车
     * @param AddCartRequest $request
     * @return array
     */
    public function add(AddCartRequest $request)
    {
        $user   = $request->user();
        $skuId  = $request->input('sku_id');
        $amount = $request->input('amount');

        // 从数据库中查询该商品是否已经在购物车中
        if ($cart = $user->cartItems()->where('product_sku_id',$skuId)->first()) {

            // 如果存在则直接叠加商品数量
            $cart->update([
                'amount' => $cart->amount + $amount,
            ]);
        }else{

            // 否则创建一个新的购物车记录
            $cart = new CartItem(['amount' => $amount]);
            //associate($user)更新「从属」关联，当更新⼀个 belongsTo 关联时，可以使⽤ associate ⽅法。此⽅法会将外键设置到下层模型
            //把 $user->id 赋值给 $cart->user_id 字段
            $cart->user()->associate($user);
            $cart->productSku()->associate($skuId);
            $cart->save();
        }
        return [];
    }


    /**
     * 购物车列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        //with(['productSku.product']) 方法用来预加载购物车里的商品和 SKU 信息,进行预加载,解决进行预加载
        $cartItems = $request->user()->cartItems()->with('productSku.product')->get();
        return view('cart.index',['cartItems'=>$cartItems]);
    }

    public function remove(ProductSku $sku,Request $request)
    {
        $request->user()->cartItems()->where('product_sku_id',$sku->id)->delete();
        return [];
    }
}
