<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Product;
use App\Models\Store;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

/**
 * Class SyncStripeProductJob
 *
 * Synchronizes product changes with Stripe connected accounts, respecting
 * multi-tenancy by referencing the store explicitly.
 *
 * Depending on the action ('created', 'updated', or 'deleted'),
 * this job creates, updates, or deletes (on Stripe) the corresponding product.
 *
 * It also syncs the product metadata from product_meta_keys and
 * product_meta_values and handles Stripe Price creation.
 *
 * When updating the product, the job will archive and create a new Stripe Price
 * only if the updated price differs from the previous price.
 */
class SyncStripeProductJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The ID of the tenant (store) in our application.
     *
     * @var int
     */
    protected int $storeId;

    /**
     * The ID of the product in our application.
     *
     * @var int
     */
    protected int $productId;

    /**
     * The action to perform ('created', 'updated', 'deleted').
     *
     * @var string
     */
    protected string $action;

    /**
     * The previous price of the product.
     *
     * If the action is "updated", this value is used to determine if the price has changed.
     *
     * @var int|null
     */
    protected ?int $previousPrice;

    /**
     * Create a new job instance.
     *
     * @param int      $storeId
     * @param int      $productId
     * @param string   $action
     * @param int|null $previousPrice The previous unit price (nullable).
     *
     * @return void
     */
    public function __construct(int $storeId, int $productId, string $action, ?int $previousPrice = null)
    {
        $this->storeId = $storeId;
        $this->productId = $productId;
        $this->action = $action;
        $this->previousPrice = $previousPrice;
    }

    /**
     * Execute the job.
     *
     * @return void
     *
     * @throws ApiErrorException
     * @throws Exception
     */
    public function handle(): void
    {
        // 1) Retrieve the Store within which this product is scoped.
        $store = Store::find($this->storeId);
        if ($store === null) {
            Log::warning(sprintf(
                'SyncStripeProductJob: Store ID %d not found.',
                $this->storeId
            ));
            return;
        }

        // 2) Find the product through the store relationship
        // and eager load the metaValues with the associated metaKey.
        $product = $store->products()
            ->with('metaValues.metaKey')
            ->find($this->productId);
        if ($product === null) {
            Log::warning(sprintf(
                'SyncStripeProductJob: Product ID %d not found in Store ID %d.',
                $this->productId,
                $this->storeId
            ));
            return;
        }

        // 3) Confirm a valid Stripe Account ID for this store.
        if (empty($store->stripe_account_id)) {
            Log::warning(sprintf(
                'SyncStripeProductJob: Stripe account not found for Store ID %d.',
                $this->storeId
            ));
            return;
        }

        // 4) Prepare metadata array from product meta values.
        $metadata = [];
        foreach ($product->metaValues as $metaValue) {
            if ($metaValue->metaKey !== null) {
                // Use the meta key as the metadata key and the meta value as the value.
                $metadata[$metaValue->metaKey->key] = $metaValue->value;
            }
        }

        // 4.5) Retrieve product images.
        // Get the main image URL.
        $mainImageUrl = $product->getFirstMediaUrl('main_image');
        $imageUrls = [];
        if ($mainImageUrl) {
            $imageUrls[] = $mainImageUrl;
        }
        // Get gallery images (limit total URLs to 8).
        $gallery = $product->getMedia('gallery_images');
        foreach ($gallery as $media) {
            if (count($imageUrls) >= 8) {
                break;
            }
            $url = $media->getUrl();
            if ($url && !in_array($url, $imageUrls)) {
                $imageUrls[] = $url;
            }
        }

        // Also append collection information as metadata.
        $product->loadMissing('collections');
        $collectionNames = $product->collections->pluck('name')->toArray();
        if (!empty($collectionNames)) {
            $metadata['Collection'] = implode(',', $collectionNames);
        }

        // 5) Initialize the Stripe client using the secret key.
        $stripe = new StripeClient(config('services.stripe.secret'));
        $stripeAccountId = $store->stripe_account_id;
        $currency = config('services.stripe.currency', 'nok');

        try {
            switch ($this->action) {
                case 'created':
                    // Create a new product in Stripe.
                    $stripeProduct = $stripe->products->create(
                        [
                            'name' => $product->name,
                            'description' => $product->description,
                            'metadata' => $metadata,
                            'images' => $imageUrls,
                        ],
                        ['stripe_account' => $stripeAccountId]
                    );

                    // Create a new price for the new product.
                    $stripePrice = $stripe->prices->create(
                        [
                            'unit_amount' => $product->price,
                            'currency' => $currency,
                            'product' => $stripeProduct->id,
                        ],
                        ['stripe_account' => $stripeAccountId]
                    );

                    // Update local record with both stripe_product_id and stripe_price_id.
                    $product->update([
                        'stripe_product_id' => (string) $stripeProduct->id,
                        'stripe_price_id' => (string) $stripePrice->id,
                    ]);
                    break;

                case 'updated':
                    // Ensure we have a Stripe product ID to update.
                    if (!empty($product->stripe_product_id)) {
                        // Update the Stripe product details.
                        $stripe->products->update(
                            $product->stripe_product_id,
                            [
                                'name' => $product->name,
                                'description' => $product->description,
                                'metadata' => $metadata,
                                'images' => $imageUrls,
                            ],
                            ['stripe_account' => $stripeAccountId]
                        );

                        // Check if the price has changed.
                        if ($this->previousPrice === null || $this->previousPrice !== $product->price) {
                            // If an old price exists, capture it for archival.
                            $oldPriceId = !empty($product->stripe_price_id) ? $product->stripe_price_id : null;

                            // Create a new Stripe price.
                            $newStripePrice = $stripe->prices->create(
                                [
                                    'unit_amount' => $product->price,
                                    'currency' => $currency,
                                    'product' => $product->stripe_product_id,
                                ],
                                ['stripe_account' => $stripeAccountId]
                            );

                            // Set the newly created price as the default price on Stripe.
                            $stripe->products->update(
                                $product->stripe_product_id,
                                [
                                    'default_price' => (string) $newStripePrice->id,
                                ],
                                ['stripe_account' => $stripeAccountId]
                            );

                            // Update the local product record with new stripe_price_id without dispatching model events.
                            \Illuminate\Database\Eloquent\Model::withoutEvents(function () use ($product, $newStripePrice) {
                                $product->update([
                                    'stripe_price_id' => (string) $newStripePrice->id,
                                ]);
                            });

                            // Archive the old price if it exists.
                            if (!empty($oldPriceId)) {
                                Log::info(sprintf(
                                    'SyncStripeProductJob: Archiving old Stripe price %s for product ID %d.',
                                    $oldPriceId,
                                    $this->productId
                                ));

                                // Insert archival record in the local archive table.
                                DB::table('product_stripe_price_archives')->insert([
                                    'product_id' => $product->id,
                                    'stripe_price_id' => $oldPriceId,
                                    'archived_at' => now(),
                                ]);

                                // Update the old Stripe price to set active to false.
                                $stripe->prices->update(
                                    $oldPriceId,
                                    ['active' => false],
                                    ['stripe_account' => $stripeAccountId]
                                );
                            }
                        }
                    } else {
                        Log::warning(sprintf(
                            'SyncStripeProductJob: No stripe_product_id for product ID %d on update.',
                            $this->productId
                        ));
                    }
                    break;

                case 'deleted':
                    // Perform deletion on Stripe if we have a valid stripe_product_id.
                    if (!empty($product->stripe_product_id)) {
                        $stripe->products->delete(
                            $product->stripe_product_id,
                            [],
                            ['stripe_account' => $stripeAccountId]
                        );
                    } else {
                        Log::warning(sprintf(
                            'SyncStripeProductJob: No stripe_product_id for product ID %d on delete.',
                            $this->productId
                        ));
                    }
                    break;

                default:
                    Log::warning(sprintf(
                        'SyncStripeProductJob: Invalid action "%s" provided for product ID %d.',
                        $this->action,
                        $this->productId
                    ));
                    break;
            }
        } catch (ApiErrorException $e) {
            Log::error(sprintf(
                'Stripe API error syncing product ID %d to Store ID %d: %s',
                $this->productId,
                $this->storeId,
                $e->getMessage()
            ));
            throw $e;
        } catch (Exception $e) {
            Log::error(sprintf(
                'General error syncing product ID %d to Store ID %d: %s',
                $this->productId,
                $this->storeId,
                $e->getMessage()
            ));
            throw $e;
        }
    }
}
