<?php

use App\Services\CheckoutSessionService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Services\StripeOrderSyncService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('stripe:sync-orders {storeId?}', function (?int $storeId) {
    $this->info('Starting synchronization of Stripe orders...');
    $start = microtime(true);

    // Define a logger closure to output verbose messages.
    $logger = function (string $message): void {
        $this->line($message);
    };

    $orderSyncService = app(StripeOrderSyncService::class);
    $orderSyncService->syncOrders($storeId, $logger);

    $this->info('Stripe orders synchronized successfully in ' . round(microtime(true) - $start, 2) . ' seconds.');
})->purpose('Synchronize orders from Stripe. Optionally specify a store ID.');

Artisan::command('stripe:sync-checkout-session {session_id} {--account=}', function ($session_id, $account) {
    // Use the provided account id option or fallback to config value.
    $accountId = $account ?: config('services.stripe.account');
    if (!$accountId) {
        $this->error("Account id is required either via the --account option or your configuration.");
        return;
    }

    $this->info("Retrieving Checkout Session: {$session_id} for account: {$accountId}");

    try {
        // Resolve the CheckoutSessionService out of the container.
        $checkoutSessionService = app(CheckoutSessionService::class);

        $checkoutSession = $checkoutSessionService->getExpandedCheckoutSession(
            $session_id,
            $accountId,
            function (string $msg): void {
                $this->info($msg);
            }
        );

        $this->info("Checkout Session retrieved successfully.");
        $this->line(print_r($checkoutSession, true));
    } catch (Exception $e) {
        $this->error("Error: " . $e->getMessage());
    }
})->purpose('Retrieve a Stripe Checkout Session with customer and line_items expanded.');
