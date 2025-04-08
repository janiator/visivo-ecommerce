<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Collection;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'store_id',
        'status',
        'name',
        'type',
        'description',
        'price',
        'stripe_product_id',
        'stripe_price_id',  // Newly added stripe_price_id attribute.
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the store that owns the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Store, \App\Models\Product>
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * The collections that this product belongs to.
     *
     * Note:
     * Uses the default pivot table name "collection_product" with "product_id"
     * and "collection_id" as foreign keys.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Collection>
     */
    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(
            Collection::class,
            'collection_product',
            'product_id',
            'collection_id'
        );
    }

    /**
     * Registers the media collections used by the product.
     *
     * The "main_images" collection is set to allow only a single file, while
     * the "gallery_images" collection supports multiple files.
     *
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('main_images')->singleFile();
        $this->addMediaCollection('gallery_images');
    }

    /**
     * The "booted" method of the model.
     *
     * Hook into the model's booting process if additional logic is required.
     *
     * @return void
     */
    protected static function booted(): void
    {
        // Additional boot logic.
    }

    /**
     * Each product can have many meta values.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function metaValues(): HasMany
    {
        return $this->hasMany(ProductMetaValue::class);
    }
}
