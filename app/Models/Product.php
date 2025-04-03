<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'store_id',
        // Note: the value for main_variant_id will be set automatically.
        'main_variant_id',
        'status',
        'name',
        'type',
        'description',
        'price',
        'short_description',
        'stripe_product_id',
    ];

    /**
     * The store that owns the product.
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Main variant reference.
     */
    public function mainVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'main_variant_id');
    }

    /**
     * All variants for the product.
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Many-to-Many relationship with Collection.
     */
    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_product');
    }

    /**
     * Register the media collections for this model.
     *
     * The main_images collection is set as a single file collection.
     * The gallery_images collection can hold multiple files.
     *
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('main_images')
            ->singleFile();

        $this->addMediaCollection('gallery_images');
    }

    /**
     * Boot the model.
     *
     * Automatically create a main variant after a product is created,
     * and delete its variants when the product is being deleted.
     */
    protected static function booted(): void
    {
        static::created(function (Product $product) {
            // If the main variant hasn't been set, automatically create it.
            if (!$product->main_variant_id) {
                $variant = $product->variants()->create([
                    'name'              => $product->name,
                    'price'             => $product->price,
                    'status'            => $product->status,
                    'grouping_attribute'=> null,
                    'metadata'          => [],
                ]);

                // Update the main_variant_id to reference the newly created variant.
                $product->update(['main_variant_id' => $variant->id]);
            }
        });

        // Delete associated variants when a product is deleted.
        static::deleting(function (Product $product): void {
            // Delete all associated product variants.
            $product->variants()->delete();
        });
    }
}
