<?php

use App\Services\CheckoutSessionService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Services\StripeOrderSyncService;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

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


//Artisan::command('stripe:import-products {--store_id=}', function () { /* The code above */ })->purpose('Import products from Stripe to the local database');
