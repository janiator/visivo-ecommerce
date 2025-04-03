<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\StripeOrderSyncService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Stripe\Stripe;

class StripeWebhookController
{
    /**
     * Handle incoming Stripe webhook events.
     *
     * @param  Request                  $request
     * @param  StripeOrderSyncService   $orderSyncService
     * @return Response
     */
    public function handleWebhook(Request $request, StripeOrderSyncService $orderSyncService): Response
    {
        // Retrieve the raw body and the Stripe-Signature header.
        $payload    = $request->getContent();
        $sigHeader  = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            // Verify the webhook signature and construct the event instance.
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (SignatureVerificationException $e) {
            // Invalid signature; return an error response.
            return response('Webhook signature verification failed.', 400);
        }

        // Depending on your integration, you might want to handle several event types.
        switch ($event->type) {
            case 'payment_intent.succeeded':
                // PaymentIntent was successful; update the order accordingly.
                // Here, we call the order sync service to process the PaymentIntent.
                $orderSyncService->syncOrders();
                break;
            case 'checkout.session.completed':
                // This event means a Checkout Session has completed.
                // You might want to process or sync detailed order info here.
                $orderSyncService->syncOrders();
                break;
            // Add more events as needed.
            default:
                // For all other events, simply acknowledge receipt.
                break;
        }

        return response('Webhook handled', 200);
    }
}
