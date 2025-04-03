<?php

namespace App\Models;

use App\Models\ProductVariant;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = [
        'store_id',
        'status',
        'name',
        'type',
        'description',
        'price',
        'short_description',
        'stripe_product_id',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function collections()
    {
        return $this->belongsToMany(Collection::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('main_images')->singleFile();

        $this->addMediaCollection('gallery_images');
    }

    protected static function booted(): void
    {
        //
    }
}
