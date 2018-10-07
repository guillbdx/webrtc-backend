<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace App\Service;

use App\Factory\TransactionFactory;
use App\Model\Subscription;
use Stripe\ApiResource;
use Stripe\Charge;
use Stripe\Stripe;
use Exception;

class StripeService
{

    /**
     * @var string
     */
    private $stripeApiKeySecret;

    /**
     * @var TransactionFactory
     */
    private $transactionFactory;

    /**
     * @param string $stripeApiKeySecret
     * @param TransactionFactory $transactionFactory
     */
    public function __construct(
        string $stripeApiKeySecret,
        TransactionFactory $transactionFactory
    )
    {
        $this->stripeApiKeySecret = $stripeApiKeySecret;
        $this->transactionFactory = $transactionFactory;
    }

    /**
     * @param Subscription $subscription
     * @param string $tokenId
     * @return ApiResource
     */
    public function charge(
        Subscription $subscription,
        string $tokenId
    ): ApiResource
    {
        Stripe::setApiKey($this->stripeApiKeySecret);
        $charge = Charge::create([
            'amount'                => $subscription->getAmount(),
            'currency'              => 'eur',
            'description'           => $subscription->getDescription(),
            'statement_descriptor'  => 'Illicam',
            'receipt_email'         => $subscription->getUser()->getEmail(),
            'source'                => $tokenId,
        ]);
        if (false === $charge->paid) {
            throw new Exception("Payment failed");
        }

        return $charge;
    }

}