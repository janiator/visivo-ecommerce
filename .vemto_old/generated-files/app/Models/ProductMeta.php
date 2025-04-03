<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductMeta extends Model
{
    use HasFactory;

    protected $table = 'product_meta';

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
