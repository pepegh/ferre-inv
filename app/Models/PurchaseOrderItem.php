<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;
    
    protected static function booted()
    {
        static::created(function ($purchaseOrderItem) {
            $product = $purchaseOrderItem->product;
            $product->increment('stock', $purchaseOrderItem->quantity);
            $product->cost_price = $purchaseOrderItem->unit_price;
            $product->save();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'quantity',
        'unit_price',
        'sub_total',
    ];

    public function purchase_order()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
