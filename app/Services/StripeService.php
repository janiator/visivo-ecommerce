<?php

declare(strict_types=1);

namespace App\Services;

use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;

/**
 * Class StripeService
 *
 * Provides methods to interact with Stripe API, including operations
 * that use Stripe Connect (e.g., creating charges, transfers, and payouts).
 */

//TODO add VatCalculator https://github.com/driesvints/vat-calculator
//TODO consider vouchers https://github.com/beyondcode/laravel-vouchers
class StripeService
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
     * Create a new connected Stripe account (for onboarding a new store).
     *
     * @param array $accountData Data required to create a connected account.
     *
     * @return \Stripe\Account
     *
     * @throws ApiErrorException if Stripe returns an error.
     */
    public function createStripeAccount(array $accountData): \Stripe\Account
    {
        return $this->stripeClient->accounts->create($accountData);
    }

    /**
     * Create a payment intent for a purchase using Stripe Connect.
     *
     * This method leverages the connected account ID from the store.
     *
     * @param array  $paymentData      Payment details.
     * @param string $connectedAccount The connected account ID from the stores table.
     *
     * @return \Stripe\PaymentIntent
     *
     * @throws ApiErrorException if Stripe returns an error.
     */
    public function createPaymentIntentForStore(array $paymentData, string $connectedAccount): \Stripe\PaymentIntent
    {
        // Important: When using Stripe Connect, pass the connected account as an option.
        return $this->stripeClient->paymentIntents->create(
            $paymentData,
            ['stripe_account' => $connectedAccount]
        );
    }

    /**
     * Initiate a payout for a connected account.
     *
     * @param array  $payoutData       Payout details.
     * @param string $connectedAccount The connected account ID.
     *
     * @return \Stripe\Payout
     *
     * @throws ApiErrorException if the payout creation fails.
     */
    public function createPayout(array $payoutData, string $connectedAccount): \Stripe\Payout
    {
        return $this->stripeClient->payouts->create(
            $payoutData,
            ['stripe_account' => $connectedAccount]
        );
    }
}

