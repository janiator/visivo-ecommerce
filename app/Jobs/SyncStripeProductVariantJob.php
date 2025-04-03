<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\ProductVariant;
use App\Services\StripeProductSyncService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stripe\Exception\ApiErrorException;

/**
 * Class SyncStripeProductVariantJob
 *
 * Handles the synchronization of a ProductVariant with Stripe upon create, update, or delete events.
 */
class SyncStripeProductVariantJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The ID of the variant to sync.
     *
     * @var int
     */
    protected int $variantId;

    /**
     * The event type: 'created', 'updated', or 'deleted'.
     *
     * @var string
     */
    protected string $eventType;

    /**
     * Create a new job instance.
     *
     * @param int $variantId
     * @param string $eventType
     */
    public function __construct(int $variantId, string $eventType)
    {
        $this->variantId = $variantId;
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
        // Load the variant along with its parent product.
        $variant = ProductVariant::with('product.store')->find($this->variantId);

        if (!$variant) {
            return;
        }

        try {
            if ($this->eventType === 'deleted') {
                // Optionally handle deletion by archiving in Stripe.
                // For example, you might call a method to mark the variant's Stripe product as inactive.
                // $stripeProductSyncService->deleteStripeProductVariant($variant);
                return;
            }

            // Sync the variant as its own Stripe product.
            // You can delegate this to a dedicated method or re-use the parent's sync method if it syncs variants as well.
            $stripeProductSyncService->syncProduct($variant->product);
        } catch (ApiErrorException $apiException) {
            \Log::error('Stripe API error during variant sync.', [
                'variant_id' => $variant->id,
                'event'      => $this->eventType,
                'error'      => $apiException->getMessage(),
            ]);
        } catch (Exception $exception) {
            \Log::error('General error during variant sync.', [
                'variant_id' => $variant->id,
                'event'      => $this->eventType,
                'error'      => $exception->getMessage(),
            ]);
        }
    }
}
