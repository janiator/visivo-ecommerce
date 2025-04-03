<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'mime_type',
        'public_url',
        'store_id',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (Image $image) {
            // If public_url is not set (or empty) and a filename exists, update it.
            if ($image->filename && empty($image->public_url)) {
                $publicUrl = Storage::disk('s3')->url($image->filename);
                $image->update(['public_url' => $publicUrl]);
            }
        });
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    // Polymorphic relations
    public function products()
    {
        return $this->morphedByMany(Product::class, 'imageables');
    }

    public function variants()
    {
        return $this->morphedByMany(ProductVariant::class, 'imageables');
    }

    public function collections()
    {
        return $this->morphedByMany(Collection::class, 'imageables');
    }
}
