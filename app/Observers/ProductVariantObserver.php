<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\ProductVariant;
use App\Jobs\SyncStripeProductVariantJob;

/**
 * Class ProductVariantObserver
 *
 * Observes events on the ProductVariant model and dispatches a job
 * to synchronize the variant with Stripe.
 */
class ProductVariantObserver
{
    /**
     * Handle the created event.
     *
     * @param ProductVariant $variant
     *
     * @return void
     */
    public function created(ProductVariant $variant): void
    {
        SyncStripeProductVariantJob::dispatch($variant->id, 'created');
    }

    /**
     * Handle the updated event.
     *
     * @param ProductVariant $variant
     *
     * @return void
     */
    public function updated(ProductVariant $variant): void
    {
        // Dispatch job only if it's already been synced (stripe_product_id exists)
        if (!empty($variant->stripe_product_id)) {
            SyncStripeProductVariantJob::dispatch($variant->id, 'updated');
        }
    }

    /**
     * Handle the deleted event.
     *
     * @param ProductVariant $variant
     *
     * @return void
     */
    public function deleted(ProductVariant $variant): void
    {
        SyncStripeProductVariantJob::dispatch($variant->id, 'deleted');
    }
}
