<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProductVariant extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'product_id',
        'name',
        'price',
        'grouping_attribute',
        'metadata',
        'status',
        'available_stock',
        'committed_stock',
        'unavailable_stock',
        'incoming_stock',
        'stripe_product_id',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Relationship to the parent product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Accessor: Calculates on-hand inventory as the sum of available, committed, and unavailable.
     *
     * @return int
     */
    public function getOnHandAttribute(): int
    {
        return (int) $this->available_stock +
            (int) $this->committed_stock +
            (int) $this->unavailable_stock;
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
        $this->addMediaCollection('variant_images')
            ->singleFile();
    }
}
