<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Product;
use App\Jobs\SyncStripeProductJob;

/**
 * Class ProductObserver
 */
class ProductObserver
{
    public function created(Product $product): void
    {
        // Dispatch the sync job only once on creation.
        // The job can later update if needed.
        SyncStripeProductJob::dispatch($product->id, 'created');
    }

    public function updated(Product $product): void
    {
        // Only dispatch the updated sync if the stripe_product_id is already set.
        // This helps to avoid duplicating the creation process.
        if (!empty($product->stripe_product_id)) {
            SyncStripeProductJob::dispatch($product->id, 'updated');
        }
    }

    public function deleted(Product $product): void
    {
        SyncStripeProductJob::dispatch($product->id, 'deleted');
    }
}
