<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use App\Jobs\SyncStripeProductJob;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProductObserverTest extends TestCase
{
    public function test_product_created_dispatches_sync_job(): void
    {
        Queue::fake();
        $product = Product::factory()->create();
        // Trigger model created event.
        $product->save();

        Queue::assertPushed(SyncStripeProductJob::class, function ($job) use ($product) {
            return $job->productId === $product->id && $job->eventType === 'created';
        });
    }
}
