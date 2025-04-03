<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StripeAccount extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = ['name', 'account_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stripeProducts()
    {
        return $this->hasMany(StripeProduct::class);
    }
}
