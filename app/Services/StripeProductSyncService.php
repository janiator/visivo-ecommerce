<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class StripeProductSyncService
 *
 * Synchronizes products and their variants with Stripe using Stripe Connect.
 * In this implementation, each variant is treated as its own Stripe product.
 *
 * @package App\Services
 */
class StripeProductSyncService
{
    protected StripeClient $stripeClient;

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
     * Sync a product and its variants to Stripe.
     *
     * Creates or updates the parent product on Stripe and then, for each variant,
     * creates or updates a separate Stripe product.
     *
     * @param Product $product The product model instance to sync.
     *
     * @return array An array containing the Stripe parent product and an array of variant Stripe products.
     *
     * @throws ApiErrorException If a Stripe API error occurs.
     * @throws Exception If the product's store is not connected.
     */
    public function syncProduct(Product $product): array
    {
        // Ensure the store has a Stripe connected account.
        if (empty($product->store->stripe_account_id)) {
            throw new Exception('Store is not connected to Stripe.');
        }

        $stripeAccount = $product->store->stripe_account_id;

        // Create or update the parent Stripe product.
        if (empty($product->stripe_product_id)) {
            // Create a new product in Stripe.
            $stripeProduct = $this->stripeClient->products->create(
                [
                    'name'        => $product->name,
                    'description' => $product->short_description ?? '',
                    'metadata'    => [
                        'local_product_id' => (string)$product->id,
                    ],
                ],
                ['stripe_account' => $stripeAccount]
            );
            // Save the Stripe product ID on the parent product.
            $product->stripe_product_id = $stripeProduct->id;
            $product->save();
        } else {
            // Update the existing Stripe product.
            $stripeProduct = $this->stripeClient->products->update(
                $product->stripe_product_id,
                [
                    'name'        => $product->name,
                    'description' => $product->short_description ?? '',
                    'metadata'    => [
                        'local_product_id' => (string)$product->id,
                    ],
                ],
                ['stripe_account' => $stripeAccount]
            );
        }

        // Process each variant as its own Stripe product.
        $variantStripeProducts = [];
        foreach ($product->variants as $variant) {
            if (empty($variant->stripe_product_id)) {
                // Create a new Stripe product for the variant.
                $stripeVariantProduct = $this->stripeClient->products->create(
                    [
                        'name'        => $variant->name . ' - ' . $product->name,
                        'description' => $variant->short_description ?? $product->short_description,
                        'metadata'    => [
                            'local_product_id' => (string)$product->id,
                            'local_variant_id' => (string)$variant->id,
                        ],
                    ],
                    ['stripe_account' => $stripeAccount]
                );
                // Persist the Stripe product ID for the variant.
                $variant->stripe_product_id = $stripeVariantProduct->id;
                $variant->save();
                $variantStripeProducts[] = $stripeVariantProduct;
            } else {
                // Update the existing Stripe product for the variant.
                $stripeVariantProduct = $this->stripeClient->products->update(
                    $variant->stripe_product_id,
                    [
                        'name'        => $variant->name . ' - ' . $product->name,
                        'description' => $variant->short_description ?? $product->short_description,
                        'metadata'    => [
                            'local_product_id' => (string)$product->id,
                            'local_variant_id' => (string)$variant->id,
                        ],
                    ],
                    ['stripe_account' => $stripeAccount]
                );
                $variantStripeProducts[] = $stripeVariantProduct;
            }
        }

        return [
            'parent_product'  => $stripeProduct,
            'variant_products' => $variantStripeProducts,
        ];
    }

    /**
     * Archive a product in Stripe upon deletion.
     *
     * Since Stripe does not support deleting products directly, we archive by setting the product inactive.
     *
     * @param Product $product The product to archive.
     *
     * @return \Stripe\Product|null
     *
     * @throws ApiErrorException If Stripe returns an error.
     */
    public function deleteStripeProduct(Product $product): ?\Stripe\Product
    {
        // Check if the product was synced to Stripe.
        if (empty($product->stripe_product_id)) {
            return null;
        }

        $stripeAccount = $product->store->stripe_account_id;

        // Archive the product by setting it to inactive.
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

        return $stripeProduct;
    }
}
