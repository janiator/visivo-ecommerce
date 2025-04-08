<?php

declare(strict_types=1);

namespace App\Observers;

use App\Jobs\SyncStripeProductJob;
use App\Models\Product;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     *
     * @param Product $product
     * @return void
     */
    public function created(Product $product): void
    {
        if (!empty($product->store_id)) {
            SyncStripeProductJob::dispatch(
                $product->store_id,
                $product->id,
                'created'
            );
        }
    }

    /**
     * Handle the Product "updated" event.
     *
     * Pass the previous price to the job so that it only archives
     * and creates a new price if the price has actually changed.
     *
     * @param Product $product
     * @return void
     */
    public function updated(Product $product): void
    {
        if (!empty($product->store_id)) {
            // Check if the 'price' attribute was changed.
            // If changed, pass the original price; otherwise pass the current price.
            $previousPrice = $product->wasChanged('price')
                ? $product->getOriginal('price')
                : $product->price;

            SyncStripeProductJob::dispatch(
                $product->store_id,
                $product->id,
                'updated',
                $previousPrice
            );
        }
    }

    /**
     * Handle the Product "deleted" event.
     *
     * @param Product $product
     * @return void
     */
    public function deleted(Product $product): void
    {
        if (!empty($product->store_id)) {
            SyncStripeProductJob::dispatch(
                $product->store_id,
                $product->id,
                'deleted'
            );
        }
    }
}
