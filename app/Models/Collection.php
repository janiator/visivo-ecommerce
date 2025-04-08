<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Collection extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'store_id',
        'name',
        'visible',
    ];

    /**
     * Get the store that owns the collection.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Store, \App\Models\Collection>
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * The products that belong to this collection.
     *
     * Note:
     * Uses the default pivot table name "collection_product" with foreign keys
     * "collection_id" for the Collection model and "product_id" for the Product model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Product>
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'collection_product',
            'collection_id',
            'product_id'
        );
    }
}
