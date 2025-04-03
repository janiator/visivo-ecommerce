<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Store extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'stripe_account_id'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
