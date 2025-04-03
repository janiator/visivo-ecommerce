<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Image extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = ['filename', 'mime_type'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function productVariants()
    {
        return $this->morphedByMany(ProductVariant::class, 'imageable');
    }

    public function collections()
    {
        return $this->morphedByMany(Collection::class, 'imageable');
    }

    public function products()
    {
        return $this->morphedByMany(Product::class, 'imageable');
    }
}
