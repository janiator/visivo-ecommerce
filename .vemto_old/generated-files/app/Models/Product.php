<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = ['main_variant_id', 'type'];

    protected $dates = ['created_at', 'updated_at'];

    public function collections()
    {
        return $this->belongsToMany(Collection::class);
    }

    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function mainVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'main_variant_id');
    }
}
