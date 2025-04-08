<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Product;
use App\Models\Store;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class Collection extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'store_id',
        'name',
        'visible',
    ];

    /**
     * Get the store that owns the collection.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Store, \App\Models\Collection>
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * The products that belong to this collection.
     *
     * Note:
     * Uses the default pivot table name "collection_product" with foreign keys
     * "collection_id" for the Collection model and "product_id" for the Product model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Product>
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'collection_product',
            'collection_id',
            'product_id'
        );
    }

    /**
     * Boot the model and add a global query scope for tenancy.
     *
     * This scope automatically filters collections by the current store (tenant) if one is set.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::addGlobalScope('tenant', function (EloquentBuilder $builder): void {
            if ($tenant = Filament::getTenant()) {
                $builder->where('store_id', $tenant->id);
            }
        });
    }


}
