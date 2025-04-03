<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Order
 *
 * @property int $id
 * @property int $store_id
 * @property int|null $customer_id
 * @property string $stripe_order_id
 * @property string|null $payment_intent
 * @property string|null $status
 * @property int $total_amount
 * @property int|null $subtotal
 * @property string $currency
 * @property array|null $shipping_address
 * @property array|null $billing_address
 * @property array|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Store $store
 * @property-read Customer|null $customer
 * @property-read \Illuminate\Database\Eloquent\Collection|OrderItem[] $orderItems
 */
class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'store_id',
        'customer_id',
        'stripe_order_id',
        'payment_intent',
        'status',
        'subtotal',
        'total_amount',
        'currency',
        'shipping_address',
        'billing_address',
        'metadata',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get the store that owns this order.
     *
     * @return BelongsTo<Store, Order>
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the customer who placed this order.
     *
     * @return BelongsTo<Customer, Order>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the order items for this order.
     *
     * @return HasMany<OrderItem>
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
