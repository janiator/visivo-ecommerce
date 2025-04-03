<?php declare(strict_types=1);

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Support\Str;
use Stripe\StripeClient;
use Exception;
use Illuminate\Support\Facades\Log;

class StripeOrderSyncService
{
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

    private function syncStoreOrders(StripeClient $stripe, Store $store, ?callable $logger = null): void
    {
        try {
            $hasMore = true;
            $startingAfter = null;

            while ($hasMore) {
                $params = [
                    'expand' => ['data.payment_intent', 'data.customer', 'data.line_items'],
                    'limit' => 100,
                ];

                if ($startingAfter) {
                    $params['starting_after'] = $startingAfter;
                }

                $checkoutSessions = $stripe->checkout->sessions->all($params, [
                    'stripe_account' => $store->stripe_account_id,
                ]);

                $hasMore = $checkoutSessions->has_more;
                if (!empty($checkoutSessions->data)) {
                    $startingAfter = end($checkoutSessions->data)->id;
                    foreach ($checkoutSessions->data as $session) {
                        $this->updateLocalOrder($session, $store, $stripe);
                    }
                } else {
                    break;
                }
            }

            if ($logger) {
                $logger("Successfully synced orders for store: {$store->name}");
            }
        } catch (\Exception $e) {
            if ($logger) {
                $logger("Failed to sync orders for store: {$store->name}. Error: {$e->getMessage()}");
            }
            Log::error("Order sync failed for store: {$store->name}", ['error' => $e->getMessage()]);
        }
    }

    private function updateLocalOrder($session, Store $store, StripeClient $stripeClient): void
    {
        $customerData = $session->customer_details;
        $customerEmail = $customerData->email ?? null;
        $billingAddress = $customerData->address ?? null;
        $shippingAddress = $session->shipping_details->address ?? null;

        $customer = null;
        if ($customerEmail) {
            $customer = Customer::updateOrCreate(
                ['email' => $customerEmail],
                [
                    'store_id' => $store->id,
                    'name' => $customerData->name ?? 'Guest',
                    'phone' => $customerData->phone ?? null,
                    'billing_address' => $billingAddress,
                    'shipping_address' => $shippingAddress,
                ]
            );
        }

        $paymentIntentId = $session->payment_intent instanceof \Stripe\PaymentIntent ? $session->payment_intent->id : $session->payment_intent;

        $order = Order::updateOrCreate(
            ['stripe_order_id' => $session->id],
            [
                'store_id' => $store->id,
                'payment_intent' => $paymentIntentId,   // Store only the payment intent ID
                'status' => $session->status,
                'subtotal' => $session->amount_subtotal,
                'customer_id' => $customer ? $customer->id : null,
                'total_amount' => $session->amount_total,
                'currency' => $session->currency,
                'shipping_address' => $shippingAddress,
                'billing_address' => $billingAddress,
                'metadata' => array_merge((array)$session->metadata, ['guest' => !$customer]),
            ]
        );

        if (!empty($session->line_items->data)) {
            foreach ($session->line_items->data as $item) {
                $productId = $this->getOrCreateProduct($item, $store, $stripeClient);
                if ($productId) {
                    OrderItem::updateOrCreate(
                        [
                            'order_id' => $order->id,
                            'product_id' => $productId,
                        ],
                        [
                            'quantity' => $item->quantity,
                            'unit_price' => $item->amount_subtotal,
                            'total_price' => $item->amount_total,
                            'name' => $item->price->id,
                        ]
                    );
                }
            }
        }
    }

    private function getOrCreateProduct($item, Store $store, StripeClient $stripeClient): ?int
    {
        if (isset($item->price->product)) {
            $stripeProductId = $item->price->product;
            $product = Product::firstWhere('stripe_product_id', $stripeProductId);

            if (!$product) {
                $stripeProduct = $stripeClient->products->retrieve($stripeProductId, [], [
                    'stripe_account' => $store->stripe_account_id
                ]);

                $product = Product::create([
                    'store_id' => $store->id,
                    'name' => $stripeProduct->name,
                    'type' => $stripeProduct->type,
                    'price' => $item->amount_total,
                    'description' => $stripeProduct->description ?? 'No description',
                    'status' => 'active',
                    'stripe_product_id' => $stripeProductId,
                ]);
            }

            return $product->id;
        }
        return null;
    }
}
