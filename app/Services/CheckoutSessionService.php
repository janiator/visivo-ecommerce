<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class CheckoutSessionService
{
    /**
     * Retrieve an expanded Checkout Session with the customer and line items expanded.
     *
     * This method uses Stripe's modern StripeClient to make a per-request configuration,
     * ensuring that the correct connected account is used (via stripe_account).
     *
     * @param string        $sessionId       The ID of the Checkout Session.
     * @param string        $stripeAccountId The connected account ID to use.
     * @param callable|null $logger          Optional logger callback to capture details.
     *
     * @return object Returns the expanded Checkout Session object as a stdClass or Stripe object.
     *
     * @throws Exception If the secret key is not configured or the session cannot be retrieved.
     */
    public function getExpandedCheckoutSession(
        string $sessionId,
        string $stripeAccountId,
        ?callable $logger = null
    ): object {
        // Retrieve the Stripe secret key from configuration file.
        $stripeSecret = config('services.stripe.secret');
        if (!$stripeSecret) {
            throw new Exception('Stripe secret key is not configured.');
        }

        // Instantiate a new StripeClient with the secret key.
        $stripe = new StripeClient($stripeSecret);

        // Define the expansion parameters to retrieve customer and line items details.
        $expand = [
            'customer',
            'line_items',
            'line_items.data.price',
        ];

        try {
            // Per-request options: specify the target connected account.
            $options = [
                'stripe_account' => $stripeAccountId,
            ];

            // Retrieve the Checkout Session using the modern StripeClient.
            $checkoutSession = $stripe->checkout->sessions->retrieve(
                $sessionId,
                ['expand' => $expand],
                $options
            );

            // Log the successful retrieval if a logger callback is provided.
            if ($logger !== null) {
                $logger("Retrieved Checkout Session {$sessionId}");
                $logger("Customer: " . print_r($checkoutSession->customer, true));
                $logger("Line Items: " . print_r($checkoutSession->line_items, true));
            }

            return $checkoutSession;
        } catch (ApiErrorException $e) {
            // Log the error message if a logger callback is provided.
            if ($logger !== null) {
                $logger("Error retrieving Checkout Session {$sessionId}: " . $e->getMessage());
            }
            throw new Exception("Unable to retrieve Checkout Session: " . $e->getMessage());
        }
    }
}
