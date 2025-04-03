<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariant extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'name',
        'price',
        'product_id',
        'grouping_attribute',
        'sku',
        'short_description',
        'description',
        'metadata',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'main_variant_id');
    }

    public function images()
    {
        return $this->morphToMany(Image::class, 'imageable');
    }
}
