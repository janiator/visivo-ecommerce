<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Customer
 *
 * @property int $id
 * @property int $store_id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Store $store
 * @property-read \Illuminate\Database\Eloquent\Collection|Order[] $orders
 */
class Customer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'store_id',
        'name',
        'email',
        'phone',
    ];

    /**
     * Get the store (tenant) that this customer belongs to.
     *
     * @return BelongsTo<Store, Customer>
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the orders for this customer.
     *
     * @return HasMany<Order>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
