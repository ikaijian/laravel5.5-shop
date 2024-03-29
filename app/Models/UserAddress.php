<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    //

    protected $fillable = [
        'province',
        'city',
        'district',
        'address',
        'zip',
        'contact_name',
        'contact_phone',
        'last_used_at',
    ];

    //表示 last_used_at 字段是一个时间日期类型，在之后的代码中 $address->last_used_at 返回的就是一个时间日期对象
    //（确切说是 Carbon 对象，Carbon 是 Laravel 默认使用的时间日期处理类）。
    protected $dates = ['last_used_at'];

//    protected $casts = [
//        'last_used_at' => 'timestamp',
//    ];

    //关联关系是一对多（一个 User 可以有多个 UserAddress，一个 UserAddress 只能属于一个 User）
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //创建了一个访问器
    //在之后的代码里可以直接通过 $address->full_address 来获取完整的地址
    public function getFullAddressAttribute()
    {
        return "{$this->province}{$this->city}{$this->district}{$this->address}";
    }
}
