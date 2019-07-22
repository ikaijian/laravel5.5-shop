<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','email_verified',
    ];

    //$casts 属性,这个字段要转换成 bool 类型
    protected $casts = [
        'email_verified' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    //关联user_addresses表，一对多关系，一个用户可以有多个地址
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }


    /**
     * 关联用户收藏的商品，多对多关系，belongsToMany
     * belongsToMany() 方法用于定义一个多对多的关联，第一个参数是关联的模型类名，第二个参数是中间表的表名
     * withTimestamps() 代表中间表带有时间戳字段
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class,'user_favorite_products')
                ->withTimestamps()
                ->orderBy('user_favorite_products.created_at','desc');
    }

    /**
     * 关联购物车
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}
