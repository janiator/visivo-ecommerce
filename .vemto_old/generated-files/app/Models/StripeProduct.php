<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StripeProduct extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'active',
        'livemode',
        'created',
        'updated',
        'description',
        'images',
        'metadata',
        'name',
        'package_dimensions',
        'shippable',
        'type',
        'unit_label',
        'url',
        'price',
        'price_id',
        'id',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'livemode' => 'boolean',
            'created' => 'datetime:t\y\p\e',
            'updated' => 'datetime:t\y\p\e',
            'metadata' => 'array',
            'package_dimensions' => 'array',
            'shippable' => 'boolean',
        ];
    }

    public function stripeAccount()
    {
        return $this->belongsTo(StripeAccount::class, 'stripe_account_id');
    }
}
