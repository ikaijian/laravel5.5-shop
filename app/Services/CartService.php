<?php
/**
 * Created by PhpStorm.
 * User: 陈开坚(jianjian)
 * Date: 2019/8/6
 * Time: 23:37
 */

namespace App\Services;


use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class CartService
{

    public function get()
    {
        //with(['productSku.product']) 方法用来预加载购物车里的商品和 SKU 信息,进行预加载,解决进行预加载
        return Auth::user()->cartItems()->with(['productSku.product'])->get();
    }

    public function add($skuId, $amount)
    {
        $user = Auth::user();

        // 从数据库中查询该商品是否已经在购物车中
        if ($item = $user->cartItems()->where('product_sku_id', $skuId)->first()) {
            // 如果存在则直接叠加商品数量
            $item->update([
                'amount' => $item->amount + $amount,
            ]);
        }else{
            // 否则创建一个新的购物车记录
            $item = new CartItem(['amount'=>$amount]);
            $item->user()->associate($user);
            $item->productSku()->associate($skuId);
            $item->save();
        }
        return $item;
    }

    public function remove($skuIds)
    {

        // 可以传单个 ID，也可以传 ID 数组
        if(!is_array($skuIds)){
            $skuIds = [$skuIds];
        }

        Auth::user()->cartItems()->whereIn('product_sku_id',$skuIds)->delete();
    }
}