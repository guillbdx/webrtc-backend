<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace App\Factory;

use App\Entity\Transaction;
use App\Entity\User;
use App\Model\Subscription;
use DateTimeImmutable;

class TransactionFactory
{

    /**
     * @param Subscription $subscription
     * @param string $stripeId
     * @return Transaction
     * @throws \Exception
     */
    public function create(
        Subscription $subscription,
        string $stripeId
    ): Transaction
    {
        $transaction = new Transaction();
        $transaction->setUser($subscription->getUser());
        $transaction->setAmount($subscription->getAmount());
        $transaction->setStripeId($stripeId);
        $transaction->setCreatedAt(new DateTimeImmutable());
        $transaction->setDescription($subscription->getDescription());

        return $transaction;
    }

}