<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductSku;

class CartItem extends Model
{
    //
    protected $fillable = ['amount'];
//    public $timestamps = false;
    public $timestamps = true;

    /**
     * 关联用户
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 关联商品sku
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productSku()
    {
        return $this->belongsTo(ProductSku::class);
    }
}
