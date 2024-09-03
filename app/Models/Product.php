<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'brand_id',
        'description',
        'price',
        'cost_price',
        'stock'
    ];

    public function brand()
    {
        return $this->belongsTo('App\Models\Brand');
    }
}
