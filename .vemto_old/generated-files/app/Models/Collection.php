<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Collection extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function products()
    {
        return $this->belongsTo(Store::class, '');
    }

    public function images()
    {
        return $this->morphToMany(Image::class, 'imageable');
    }
}
