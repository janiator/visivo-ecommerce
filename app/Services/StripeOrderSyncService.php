<?php declare(strict_types=1);

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use Exception;

class StripeOrderSyncService
{
    /**
     * Synchronize orders from Stripe.
     *
     * @param int|null      $storeId
     * @param callable|null $logger
     *
     * @throws Exception
     */
    public function syncOrders(?int $storeId = null, ?callable $logger = null): void
    {
        $stripeSecret = config('services.stripe.secret');
        if (!$stripeSecret) {
            throw new Exception('Stripe secret key is not configured.');
        }

        $stripe = new StripeClient($stripeSecret);
        $stores = $storeId
            ? Store::where('id', $storeId)->whereNotNull('stripe_account_id')->get()
            : Store::whereNotNull('stripe_account_id')->get();

        foreach ($stores as $store) {
            $this->syncStoreOrders($stripe, $store, $logger);
        }
    }

    /**
     * Synchronize orders for a particular store.
     *
     * @param StripeClient  $stripe
     * @param Store         $store
     * @param callable|null $logger
     *
     * @return void
     */
    private function syncStoreOrders(StripeClient $stripe, Store $store, ?callable $logger = null): void
    {
        try {
            $hasMore       = true;
            $startingAfter = null;

            while ($hasMore) {
                $params = [
                    'expand' => ['data.payment_intent', 'data.customer', 'data.line_items'],
                    'limit'  => 100,
                ];
                if ($startingAfter) {
                    $params['starting_after'] = $startingAfter;
                }
                $checkoutSessions = $stripe->checkout->sessions->all(
                    $params,
                    ['stripe_account' => $store->stripe_account_id]
                );

                $hasMore = $checkoutSessions->has_more;

                if (!empty($checkoutSessions->data)) {
                    // Sort sessions from oldest to newest by their Stripe created timestamp.
                    $sessions = $checkoutSessions->data;
                    usort(
                        $sessions,
                        static function ($a, $b): int {
                            return $a->created <=> $b->created;
                        }
                    );
                    $startingAfter = end($checkoutSessions->data)->id;

                    foreach ($sessions as $session) {
                        $this->updateLocalOrder($session, $store, $stripe);
                    }
                } else {
                    break;
                }
            }

            if ($logger) {
                $logger("Successfully synced orders for store: {$store->name}");
            }
        } catch (Exception $e) {
            if ($logger) {
                $logger("Failed to sync orders for store: {$store->name}. Error: {$e->getMessage()}");
            }
            Log::error("Order sync failed for store: {$store->name}", ['error' => $e->getMessage()]);
        }
    }

    /**
     * Clean metadata by converting it to a JSON string and then decoding it back to an array.
     * This removes any private or internal attributes.
     *
     * @param mixed $metadata
     *
     * @return array
     */
    private function cleanMetadata($metadata): array
    {
        $cleanData = json_decode(json_encode($metadata), true);
        return is_array($cleanData) ? $cleanData : [];
    }

    /**
     * Update or create a local order based on the Stripe session data.
     *
     * @param mixed        $session
     * @param Store        $store
     * @param StripeClient $stripeClient
     *
     * @return void
     */
    private function updateLocalOrder($session, Store $store, StripeClient $stripeClient): void
    {
        $customerData    = $session->customer_details;
        $customerEmail   = $customerData->email ?? null;
        $billingAddress  = $customerData->address ?? null;
        $shippingAddress = $session->shipping_details->address ?? null;
        $customer        = null;

        if ($customerEmail) {
            $customer = Customer::updateOrCreate(
                ['email' => $customerEmail],
                [
                    'store_id'         => $store->id,
                    'name'             => $customerData->name ?? 'Guest',
                    'phone'            => $customerData->phone ?? null,
                    'billing_address'  => $billingAddress,
                    'shipping_address' => $shippingAddress,
                ]
            );
        }

        $paymentIntentId = $session->payment_intent instanceof \Stripe\PaymentIntent
            ? $session->payment_intent->id
            : $session->payment_intent;

        // Clean metadata to remove unsupported characters.
        $originalMetadata = $this->cleanMetadata($session->metadata);
        $mergedMetadata   = array_merge($originalMetadata, ['guest' => !$customer]);

        // Convert the Stripe session's created timestamp into a Carbon instance.
        $stripeTimestamp = isset($session->created)
            ? Carbon::createFromTimestamp((int) $session->created)
            : Carbon::now();

        // First, create or update without overriding created_at/updated_at.
        $order = Order::updateOrCreate(
            ['stripe_order_id' => $session->id],
            [
                'store_id'         => $store->id,
                'payment_intent'   => $paymentIntentId,
                'status'           => $session->status,
                'subtotal'         => $session->amount_subtotal,
                'customer_id'      => $customer ? $customer->id : null,
                'total_amount'     => $session->amount_total,
                'currency'         => $session->currency,
                'shipping_address' => $shippingAddress,
                'billing_address'  => $billingAddress,
                'metadata'         => $mergedMetadata,
            ]
        );

        // Then override created_at and updated_at from Stripe while preserving global Eloquent behavior.
        $order->timestamps = false; // Temporarily disable automatic timestamp updates.
        $order->created_at = $stripeTimestamp;
        $order->updated_at = $stripeTimestamp;
        $order->saveQuietly();
        $order->timestamps = true; // Restore normal behavior for future operations.

        if (!empty($session->line_items->data)) {
            foreach ($session->line_items->data as $item) {
                $productId = $this->getOrCreateProduct($item, $store, $stripeClient);
                if ($productId) {
                    OrderItem::updateOrCreate(
                        [
                            'order_id'   => $order->id,
                            'product_id' => $productId,
                        ],
                        [
                            'quantity'    => $item->quantity,
                            'unit_price'  => $item->amount_subtotal,
                            'total_price' => $item->amount_total,
                            'name'        => $item->price->id,
                        ]
                    );
                }
            }
        }
    }

    /**
     * Retrieve or create a product based on a Stripe order item.
     *
     * @param mixed        $item
     * @param Store        $store
     * @param StripeClient $stripeClient
     *
     * @return int|null
     */
    private function getOrCreateProduct($item, Store $store, StripeClient $stripeClient): ?int
    {
        if (isset($item->price->product)) {
            $stripeProductId = $item->price->product;
            $product         = Product::firstWhere('stripe_product_id', $stripeProductId);
            if (!$product) {
                $stripeProduct = $stripeClient->products->retrieve(
                    $stripeProductId,
                    [],
                    ['stripe_account' => $store->stripe_account_id]
                );
                $product = Product::create([
                    'store_id'          => $store->id,
                    'name'              => $stripeProduct->name,
                    'type'              => $stripeProduct->type,
                    'price'             => $item->amount_total,
                    'description'       => $stripeProduct->description ?? 'No description',
                    'status'            => 'active',
                    'stripe_product_id' => $stripeProductId,
                ]);
            }

            return $product->id;
        }

        return null;
    }
}
