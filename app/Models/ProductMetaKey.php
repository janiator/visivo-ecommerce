<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductMetaKey extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_meta_keys';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'store_id',
        'key',
        'data_type',
    ];

    /**
     * Get all meta values associated with this meta key.
     */
    public function metaValues(): HasMany
    {
        return $this->hasMany(ProductMetaValue::class, 'meta_key_id');
    }
}
