<?php declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Store;
use App\Models\Collection as ProductCollection;
use App\Models\ProductMetaKey;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Stripe\ApiErrorException;
use Stripe\StripeClient;

class ImportStripeProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stripe:import-products {--store_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from Stripe to the local database';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $stripeSecret = config('services.stripe.secret');

        if (empty($stripeSecret)) {
            $this->error('Stripe secret key is not configured.');
            return;
        }

        $stripeClient = new StripeClient($stripeSecret);

        $storeQuery = Store::query()->whereNotNull('stripe_account_id');
        if ($this->option('store_id')) {
            $storeQuery->where('id', (int) $this->option('store_id'));
        }
        $stores = $storeQuery->get();

        if ($stores->isEmpty()) {
            $this->info('No stores with connected Stripe accounts found.');
            return;
        }

        foreach ($stores as $store) {
            $stripeAccount = (string) $store->stripe_account_id;
            $this->info("Processing store [{$store->id}] {$store->name}");

            try {
                $stripeProducts = $stripeClient->products->all(
                    ['limit' => 100],
                    ['stripe_account' => $stripeAccount]
                );
            } catch (ApiErrorException $e) {
                $this->error("Failed to retrieve products for store {$store->id}: " . $e->getMessage());
                continue;
            }

            foreach ($stripeProducts->data as $stripeProduct) {
                // Convert metadata to an array.
                $metadataArray = $stripeProduct->metadata instanceof \Stripe\StripeObject
                    ? $stripeProduct->metadata->toArray()
                    : (array) $stripeProduct->metadata;

                // Debug output of incoming metadata.
                $this->info("Metadata from Stripe:");
                print_r($metadataArray);

                // Retrieve _local_product_id from metadata.
                $localProductId = $metadataArray['_local_product_id'] ?? null;

                // Use lower case for the collection name.
                $collectionName = (isset($metadataArray['Collection']) && $metadataArray['Collection'])
                    ? strtolower($metadataArray['Collection'])
                    : null;

                // Retrieve the main image url if available.
                $imageUrl = (isset($stripeProduct->images) && is_array($stripeProduct->images) && count($stripeProduct->images) > 0)
                    ? (string) $stripeProduct->images[0]
                    : null;

                // Retrieve the product price from the default price.
                $price = 0;
                if (isset($stripeProduct->default_price) && $stripeProduct->default_price) {
                    // default_price can be just an id or expanded in the object.
                    if (is_string($stripeProduct->default_price)) {
                        $price = $this->getDefaultPrice($stripeClient, $stripeProduct->default_price, $stripeAccount);
                    } elseif (isset($stripeProduct->default_price->id)) {
                        // In case it comes expanded.
                        $price = $stripeProduct->default_price->unit_amount ?? 0;
                    }
                }

                // Find the local product by _local_product_id or by stripe_product_id.
                $localProduct = $localProductId
                    ? Product::find($localProductId)
                    : Product::where('stripe_product_id', $stripeProduct->id)->first();

                if ($localProduct) {
                    // Update local product data.
                    $localProduct->forceFill([
                        'name'              => $stripeProduct->name,
                        'description'       => $stripeProduct->description,
                        'stripe_product_id' => $stripeProduct->id,
                        'price'             => $price, // update local price with default Stripe price
                    ])->save();

                    // If metadata doesn't include _local_product_id, update it.
                    if (empty($metadataArray['_local_product_id'])) {
                        $metadataArray['_local_product_id'] = (string)$localProduct->id;
                        $this->updateStripeMetadata($stripeClient, $stripeProduct->id, $metadataArray, $stripeAccount);
                    }

                    // Update meta values via the new EAV setup.
                    $this->updateLocalMetaValues($localProduct, $metadataArray);

                    // Remove duplicates (if any) with the same stripe_product_id.
                    $duplicates = Product::where('stripe_product_id', $stripeProduct->id)
                        ->where('id', '!=', $localProduct->id)
                        ->get();

                    foreach ($duplicates as $duplicate) {
                        $duplicate->delete();
                        $this->info("Deleted duplicate product ID {$duplicate->id} for Stripe product {$stripeProduct->id}");
                    }

                    $this->info("Updated product ID {$localProduct->id} from Stripe product {$stripeProduct->id}");
                    $this->logLocalMetadata($localProduct);

                    // Attach the main image if provided and not already attached.
                    if ($imageUrl && !$localProduct->getFirstMedia('main_image')) {
                        try {
                            $localProduct->addMediaFromUrl($imageUrl)
                                ->toMediaCollection('main_image');
                            $this->info("Attached main image for product ID {$localProduct->id}");
                        } catch (\Throwable $e) {
                            Log::error("Failed to attach image for product {$localProduct->id}: " . $e->getMessage());
                        }
                    }

                    // Attach to collection if specified.
                    if ($collectionName) {
                        $this->attachProductToCollection($localProduct, $store->id, $collectionName);
                    }
                } else {
                    // Create a new product without firing events.
                    $newProduct = Product::withoutEvents(static function () use ($store, $stripeProduct, $price): Product {
                        return Product::create([
                            'store_id'          => $store->id,
                            'name'              => $stripeProduct->name,
                            'description'       => $stripeProduct->description,
                            'stripe_product_id' => $stripeProduct->id,
                            'status'            => 'active',
                            'price'             => $price,
                            'type'              => 'default',
                        ]);
                    });

                    // Add _local_product_id into metadata.
                    $metadataArray['_local_product_id'] = (string)$newProduct->id;
                    $this->updateStripeMetadata($stripeClient, $stripeProduct->id, $metadataArray, $stripeAccount);

                    // Update meta values via the new EAV setup.
                    $this->updateLocalMetaValues($newProduct, $metadataArray);

                    $this->info("Imported new product ID {$newProduct->id} from Stripe product {$stripeProduct->id}");
                    $this->logLocalMetadata($newProduct);

                    if ($imageUrl) {
                        try {
                            $newProduct->addMediaFromUrl($imageUrl)
                                ->toMediaCollection('main_image');
                            $this->info("Attached main image for new product ID {$newProduct->id}");
                        } catch (\Throwable $e) {
                            Log::error("Failed to attach image for new product {$newProduct->id}: " . $e->getMessage());
                        }
                    }

                    if ($collectionName) {
                        $this->attachProductToCollection($newProduct, $store->id, $collectionName);
                    }
                }
            }
        }

        $this->info('Stripe product import completed.');
    }

    /**
     * Retrieve the default price from Stripe based on a price ID.
     *
     * @param StripeClient $stripeClient
     * @param string       $defaultPriceId
     * @param string       $stripeAccount
     *
     * @return int Returns the unit amount (in cents) or 0 if not found.
     */
    protected function getDefaultPrice(StripeClient $stripeClient, string $defaultPriceId, string $stripeAccount): int
    {
        try {
            $priceObject = $stripeClient->prices->retrieve(
                $defaultPriceId,
                [],
                ['stripe_account' => $stripeAccount]
            );

            return (int) ($priceObject->unit_amount ?? 0);
        } catch (ApiErrorException $e) {
            Log::error("Failed to retrieve default price for price ID {$defaultPriceId}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Update metadata for a Stripe product.
     *
     * @param StripeClient $stripeClient
     * @param string       $stripeProductId
     * @param array        $metadata
     * @param string       $stripeAccount
     *
     * @return void
     */
    protected function updateStripeMetadata(StripeClient $stripeClient, string $stripeProductId, array $metadata, string $stripeAccount): void
    {
        try {
            $stripeClient->products->update(
                $stripeProductId,
                ['metadata' => $metadata],
                ['stripe_account' => $stripeAccount]
            );
        } catch (ApiErrorException $e) {
            Log::error("Failed to update metadata on Stripe product {$stripeProductId}: " . $e->getMessage());
        }
    }

    /**
     * Update the product's meta values using the new EAV approach.
     *
     * @param \App\Models\Product $product
     * @param array               $metadataArray
     *
     * @return void
     */
    protected function updateLocalMetaValues(Product $product, array $metadataArray): void
    {
        // Remove all existing meta value records.
        $product->metaValues()->delete();

        // Loop through each key/value pair and create new meta values.
        foreach ($metadataArray as $key => $value) {
            // Optionally, you might choose to skip keys that belong to internal logic.
            // In this example we store all keys.
            $metaKey = ProductMetaKey::firstOrCreate(
                [
                    'store_id' => $product->store_id,
                    'key'      => $key,
                ],
                [
                    'data_type' => 'string',
                ]
            );

            $product->metaValues()->create([
                'meta_key_id' => $metaKey->id,
                'value'       => $value,
            ]);
        }
    }

    /**
     * Attach a product to a collection.
     *
     * If the specified collection does not exist for the store, create it.
     *
     * @param \App\Models\Product $product
     * @param int                 $storeId
     * @param string              $collectionName
     *
     * @return void
     */
    protected function attachProductToCollection(Product $product, int $storeId, string $collectionName): void
    {
        $this->info("Attempting to attach collection: '{$collectionName}' for store ID: {$storeId}");
        $collection = ProductCollection::firstOrCreate(
            [
                'store_id' => $storeId,
                'name'     => $collectionName,
            ],
            [
                'visible' => true,
            ]
        );
        $this->info("Found or created collection with ID: {$collection->id}");
        $product->collections()->syncWithoutDetaching($collection->id);
        $this->info("Attached product ID {$product->id} to collection '{$collectionName}' (ID: {$collection->id})");
    }

    /**
     * Log local product metadata for debugging purposes.
     *
     * @param \App\Models\Product $product
     *
     * @return void
     */
    protected function logLocalMetadata(Product $product): void
    {
        $this->info("Local meta values for product ID {$product->id}:");
        // Loading meta values with its meta key relation.
        $product->loadMissing('metaValues.metaKey');
        $metaData = [];
        foreach ($product->metaValues as $metaValue) {
            if ($metaValue->metaKey !== null) {
                $metaData[$metaValue->metaKey->key] = $metaValue->value;
            }
        }
        print_r($metaData);
    }
}
