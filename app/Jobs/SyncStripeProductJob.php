<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Product;
use App\Services\StripeProductSyncService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;

/**
 * Class SyncStripeProductJob
 *
 * Handles the synchronization of a Product with Stripe on create, update,
 * or delete events.
 *
 * @package App\Jobs
 */
class SyncStripeProductJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The ID of the product to sync.
     *
     * @var int
     */
    protected int $productId;

    /**
     * The type of event triggering this sync ('created', 'updated', 'deleted').
     *
     * @var string
     */
    protected string $eventType;

    /**
     * Create a new job instance.
     *
     * @param int    $productId
     * @param string $eventType
     */
    public function __construct(int $productId, string $eventType)
    {
        $this->productId = $productId;
        $this->eventType = $eventType;
    }

    /**
     * Execute the job.
     *
     * @param StripeProductSyncService $stripeProductSyncService
     *
     * @return void
     */
    public function handle(StripeProductSyncService $stripeProductSyncService): void
    {
        // For 'deleted' events, the product might no longer be present locally.
        if ($this->eventType === 'deleted') {
            // If possible, retrieve from queue payload, or use soft delete logic.
            $product = Product::withTrashed()->with('store')->find($this->productId);
            if ($product) {
                // Archive the product on Stripe.
                try {
                    $stripeProductSyncService->deleteStripeProduct($product);
                } catch (ApiErrorException $apiException) {
                    Log::error(
                        'Stripe API error during product deletion sync.',
                        [
                            'product_id' => $this->productId,
                            'error'      => $apiException->getMessage(),
                        ]
                    );
                }
            }
            return;
        }

        // For create or update events.
        $product = Product::with('variants', 'store')->find($this->productId);
        if ($product) {
            try {
                $stripeProductSyncService->syncProduct($product);
            } catch (ApiErrorException $apiException) {
                \Log::error(
                    'Stripe API error during product sync.',
                    [
                        'product_id' => $this->productId,
                        'event'      => $this->eventType,
                        'error'      => $apiException->getMessage(),
                    ]
                );
            } catch (Exception $exception) {
                \Log::error(
                    'General error during product sync.',
                    [
                        'product_id' => $this->productId,
                        'event'      => $this->eventType,
                        'error'      => $exception->getMessage(),
                    ]
                );
            }
        }
    }
}
