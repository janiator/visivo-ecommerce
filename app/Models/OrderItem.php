<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class OrderItem
 *
 * @property int $id
 * @property int $order_id
 * @property int|null $product_id
 * @property int|null $product_variant_id
 * @property int $quantity
 * @property int $unit_price
 * @property int $total_price
 * @property string|null $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Order $order
 */
class OrderItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'unit_price',
        'total_price',
        'name',
    ];

    /**
     * Get the order that owns this item.
     *
     * @return BelongsTo<Order, OrderItem>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
