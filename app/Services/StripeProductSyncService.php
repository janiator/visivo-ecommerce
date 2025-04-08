<?php declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use Exception;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Illuminate\Support\Facades\Log;

/**
 * Class StripeProductSyncService
 *
 * Synchronizes products and their variants with Stripe using Stripe Connect.
 * In addition to sending metadata to Stripe, this service stores a clean metadata
 * payload on the local Product and variant models.
 *
 * Optionally, you can force syncing of archived products if desired.
 *
 * @package App\Services
 */
class StripeProductSyncService
{
    protected StripeClient $stripeClient;

    /**
     * If true, then even products that are archived (active = false) on Stripe
     * will be synced (and reactivated) during the sync process.
     *
     * @var bool
     */
    protected bool $forceSyncArchived = false;

    /**
     * Constructor.
     *
     * Initializes the StripeClient using the Stripe secret key from configuration.
     */
    public function __construct()
    {
        $this->stripeClient = new StripeClient(config('services.stripe.secret'));
    }

    /**
     * Set whether to force sync archived products.
     *
     * @param bool $flag
     * @return void
     */
    public function setForceSyncArchived(bool $flag): void
    {
        $this->forceSyncArchived = $flag;
    }

    /**
     * Build the metadata for a product.
     *
     * @param Product $product
     * @return array
     */
    protected function getProductMetadata(Product $product): array
    {
        return [
            'local_product_id' => (string)$product->id,
            // Add more product metadata as needed.
        ];
    }

    /**
     * Build the metadata for a product variant.
     *
     * @param Product $product
     * @param mixed $variant
     * @return array
     */
    protected function getVariantMetadata(Product $product, $variant): array
    {
        return [
            'local_product_id'  => (string)$product->id,
            'local_variant_id'  => (string)$variant->id,
            // Add more variant metadata as needed.
        ];
    }

    /**
     * Sync a product and its variants to Stripe.
     *
     * Creates or updates the parent product on Stripe and then, for each variant,
     * creates or updates a separate Stripe product.
     * Additionally, stores a clean metadata payload in the local database.
     *
     * @param Product $product
     * @return array
     *
     * @throws ApiErrorException
     * @throws Exception
     */
    public function syncProduct(Product $product): array
    {
        // Ensure the store has a Stripe connected account.
        if (empty($product->store->stripe_account_id)) {
            throw new Exception('Store is not connected to Stripe.');
        }

        $stripeAccount = $product->store->stripe_account_id;
        $productMetadata = $this->getProductMetadata($product);
        Log::info("Metadata: {$productMetadata})");

        // If product already has a Stripe ID, optionally allow syncing archived products.
        $shouldSync = true;
        if (!empty($product->stripe_product_id)) {
            // Retrieve current Stripe product status.
            $stripeProduct = $this->stripeClient->products->retrieve(
                $product->stripe_product_id,
                ['stripe_account' => $stripeAccount]
            );
            if (!$stripeProduct->active && !$this->forceSyncArchived) {
                Log::info("Skipping sync for archived product (ID: {$product->id})");
                $shouldSync = false;
            }
        }

        if ($shouldSync) {
            if (empty($product->stripe_product_id)) {
                // Create new product in Stripe.
                $stripeProduct = $this->stripeClient->products->create(
                    [
                        'name'        => $product->name,
                        'description' => $product->short_description ?? '',
                        'metadata'    => $productMetadata,
                    ],
                    ['stripe_account' => $stripeAccount]
                );
                $product->stripe_product_id = $stripeProduct->id;
            } else {
                // Optionally reactivate the product if it's archived.
                $updateParams = [
                    'name'        => $product->name,
                    'description' => $product->short_description ?? '',
                    'metadata'    => $productMetadata,
                ];
                if ($this->forceSyncArchived && isset($stripeProduct) && !$stripeProduct->active) {
                    $updateParams['active'] = true;
                    Log::info("Reactivating archived product (ID: {$product->id})");
                }
                $stripeProduct = $this->stripeClient->products->update(
                    $product->stripe_product_id,
                    $updateParams,
                    ['stripe_account' => $stripeAccount]
                );
            }
            // Store updated metadata locally.
            $product->metadata = $productMetadata;
            $product->save();
        } else {
            // If not syncing, retrieve the latest Stripe product for consistency.
            $stripeProduct = $this->stripeClient->products->retrieve(
                $product->stripe_product_id,
                ['stripe_account' => $stripeAccount]
            );
        }


        return [
            'parent_product'   => $stripeProduct
        ];
    }

    /**
     * Archive a product on Stripe and update local metadata.
     *
     * @param Product $product
     * @return \Stripe\Product|null
     *
     * @throws ApiErrorException
     */
    public function deleteStripeProduct(Product $product): ?\Stripe\Product
    {
        if (empty($product->stripe_product_id)) {
            return null;
        }

        $stripeAccount = $product->store->stripe_account_id;
        $stripeProduct = $this->stripeClient->products->update(
            $product->stripe_product_id,
            [
                'active'   => false,
                'metadata' => [
                    'archived_at' => now()->toIso8601String(),
                ],
            ],
            ['stripe_account' => $stripeAccount]
        );

        // Update local metadata.
        $product->metadata = ['archived_at' => now()->toIso8601String()];
        $product->save();

        Log::info("Archived product (ID: {$product->id}) on Stripe.");

        return $stripeProduct;
    }
}
