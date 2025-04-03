<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ProductVariant;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Exception;

/**
 * Class StripeVariantSyncService
 *
 * Syncs each product variant as a separate Stripe product.
 *
 * @package App\Services
 */
class StripeVariantSyncService
{
    protected StripeClient $stripeClient;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->stripeClient = new StripeClient(config('services.stripe.secret'));
    }

    /**
     * Sync a product variant with Stripe.
     *
     * This creates or updates a Stripe product for the given product variant.
     *
     * @param ProductVariant $variant The local variant to sync.
     *
     * @return \Stripe\Product
     *
     * @throws ApiErrorException If a Stripe API error occurs.
     * @throws Exception         If the variant's store is not connected to Stripe.
     */
    public function syncVariant(ProductVariant $variant): \Stripe\Product
    {
        // Ensure the product's store is connected to Stripe.
        $store = $variant->product->store;
        if (empty($store->stripe_account_id)) {
            throw new Exception('Store is not connected to Stripe.');
        }

        $stripeAccount = $store->stripe_account_id;

        if (empty($variant->stripe_product_id)) {
            // Create a new Stripe product for the variant.
            $stripeProduct = $this->stripeClient->products->create(
                [
                    'name'        => $variant->name . ' - ' . $variant->product->name,
                    'description' => $variant->short_description ?? $variant->product->short_description,
                    'metadata'    => [
                        'local_product_id'  => (string)$variant->product->id,
                        'local_variant_id'  => (string)$variant->id,
                    ],
                ],
                ['stripe_account' => $stripeAccount]
            );

            // Store the Stripe product ID
            $variant->stripe_product_id = $stripeProduct->id;
            $variant->save();
        } else {
            // Update the existing Stripe product.
            $stripeProduct = $this->stripeClient->products->update(
                $variant->stripe_product_id,
                [
                    'name'        => $variant->name . ' - ' . $variant->product->name,
                    'description' => $variant->short_description ?? $variant->product->short_description,
                    'metadata'    => [
                        'local_product_id'  => (string)$variant->product->id,
                        'local_variant_id'  => (string)$variant->id,
                    ],
                ],
                ['stripe_account' => $stripeAccount]
            );
        }

        // Optionally, create or update a price object for this variant if needed.

        return $stripeProduct;
    }
}
