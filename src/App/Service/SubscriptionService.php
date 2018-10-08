<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace App\Service;

use App\Entity\User;
use App\Model\Subscription;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class SubscriptionService
{

    const MAX_TRIAL_DURATION    = 86400;    // 24 hours = 86400 seconds

    const MONTHLY_PRICE         = 890;

    const MAX_ARCHIVED_PHOTOS   = 10080;        // 7 days = 10080 minutes

    const STATUS_TRIAL          = 'STATUS_TRIAL';
    const STATUS_SUBSCRIBED     = 'STATUS_SUBSCRIBED';
    const STATUS_NOTHING        = 'STATUS_NOTHING';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param User $user
     * @param int $time
     */
    public function incrementUseDuration(User $user, int $time): void
    {
        $useDuration = $user->getUseDuration();
        $useDuration += $time;
        $user->setUseDuration($useDuration);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isWithinTrial(User $user): bool
    {
        return $user->getUseDuration() < self::MAX_TRIAL_DURATION;
    }

    /**
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    public function isSubscribed(User $user): bool
    {
        if ($user->getSubscribedTill() > new DateTimeImmutable()) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @return string
     */
    public function getUserStatus(User $user): string
    {
        if ($this->isSubscribed($user)) {
            return self::STATUS_SUBSCRIBED;
        }
        if ($this->isWithinTrial($user)) {
            return self::STATUS_TRIAL;
        }

        return self::STATUS_NOTHING;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canUseTheApplication(User $user): bool
    {
        if (self::STATUS_NOTHING === $this->getUserStatus($user)) {
            return false;
        }

        return true;
    }

    /**
     * @param Subscription $subscription
     */
    public function applySubscription(Subscription $subscription): void
    {
        $user = $subscription->getUser();
        $user->setSubscribedTill($subscription->getEndAt());
        $this->entityManager->flush();
    }

}