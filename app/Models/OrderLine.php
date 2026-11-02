<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderLine extends BaseModel
{
    protected $table = 'order_lines';

    public $fillable = [
        'order_id',
        'product_id',
        'price',
        'sale_price',
        'is_sale',
        'quantity',
    ];

    protected $casts = [
        'is_sale' => 'boolean',
        'price' => 'int',
        'sale_price' => 'int',
        'quantity' => 'int',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
