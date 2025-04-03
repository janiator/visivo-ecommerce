<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'name',
        'visible',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    // Many-to-Many with Product
    public function products()
    {
        return $this->belongsToMany(Product::class, 'collection_product');
    }

}
