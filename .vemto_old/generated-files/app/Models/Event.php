<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function tickets()
    {
        return $this->hasManyThrough(
            ProductVariant::class,
            Product::class,
            'id'
        );
    }
}
