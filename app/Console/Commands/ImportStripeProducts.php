<?php declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Store;
use App\Models\Collection as ProductCollection;
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
            $storeQuery->where('id', (int)$this->option('store_id'));
        }

        $stores = $storeQuery->get();

        if ($stores->isEmpty()) {
            $this->info('No stores with connected Stripe accounts found.');
            return;
        }

        foreach ($stores as $store) {
            $stripeAccount = (string)$store->stripe_account_id;
            $this->info("Processing store [{$store->id}] {$store->name}");

            try {
                $stripeProducts = $stripeClient->products->all(['limit' => 100], ['stripe_account' => $stripeAccount]);
            } catch (ApiErrorException $e) {
                $this->error("Failed to retrieve products for store {$store->id}: " . $e->getMessage());
                continue;
            }

            foreach ($stripeProducts->data as $stripeProduct) {
                // Convert metadata to an array.
                $metadataArray = $stripeProduct->metadata instanceof \Stripe\StripeObject
                    ? $stripeProduct->metadata->toArray()
                    : (array)$stripeProduct->metadata;

                // Debug output of incoming metadata.
                $this->info("Metadata from Stripe:");
                print_r($metadataArray);

                // Retrieve local_product_id from metadata.
                $localProductId = $metadataArray['local_product_id'] ?? null;
                // Retrieve the collection value, normalized to lowercase when present.
                $collectionName = (isset($metadataArray['Collection']) && $metadataArray['Collection'])
                    ? strtolower($metadataArray['Collection'])
                    : null;
                // Retrieve the main image URL if available.
                $imageUrl = (isset($stripeProduct->images) && is_array($stripeProduct->images) && count($stripeProduct->images) > 0)
                    ? (string)$stripeProduct->images[0]
                    : null;

                // Find the local product by local_product_id or by stripe_product_id.
                $localProduct = $localProductId
                    ? Product::find($localProductId)
                    : Product::where('stripe_product_id', $stripeProduct->id)->first();

                if ($localProduct) {
                    // Update local product data and metadata.
                    $localProduct->forceFill([
                        'name'              => $stripeProduct->name,
                        'description'       => $stripeProduct->description,
                        'stripe_product_id' => $stripeProduct->id,
                        'metadata'          => $metadataArray,
                    ])->save();

                    // If metadata doesn't include local_product_id, update it.
                    if (empty($metadataArray['local_product_id'])) {
                        $metadataArray['local_product_id'] = (string)$localProduct->id;
                        $this->updateStripeMetadata($stripeClient, $stripeProduct->id, $metadataArray, $stripeAccount);

                        $localProduct->forceFill(['metadata' => $metadataArray])->save();
                    }

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
                    $newProduct = Product::withoutEvents(static function () use ($store, $stripeProduct): Product {
                        return Product::create([
                            'store_id'          => $store->id,
                            'name'              => $stripeProduct->name,
                            'description'       => $stripeProduct->description,
                            'stripe_product_id' => $stripeProduct->id,
                            'status'            => 'active',
                            'price'             => 0,
                            'type'              => 'default',
                        ]);
                    });

                    // Add local_product_id into metadata.
                    $metadataArray['local_product_id'] = (string)$newProduct->id;
                    $this->updateStripeMetadata($stripeClient, $stripeProduct->id, $metadataArray, $stripeAccount);

                    $newProduct->forceFill(['metadata' => $metadataArray])->save();
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
        $this->info("Local metadata for product ID {$product->id}:");
        print_r($product->metadata);
    }
}
