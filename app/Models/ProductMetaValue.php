<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductMetaValue extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_meta_values';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'meta_key_id',
        'value',
    ];

    /**
     * Get the product that this meta value belongs to.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the meta key record that this value belongs to.
     */
    public function metaKey(): BelongsTo
    {
        return $this->belongsTo(ProductMetaKey::class, 'meta_key_id');
    }
}
