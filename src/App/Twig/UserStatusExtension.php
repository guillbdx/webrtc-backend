<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace App\Twig;

use App\Entity\User;
use App\Service\SubscriptionService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class UserStatusExtension extends AbstractExtension
{

    /**
     * @var SubscriptionService
     */
    private $subscriptionService;

    /**
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(
        SubscriptionService $subscriptionService
    )
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * @return array|\Twig_Filter[]
     */
    public function getFilters()
    {
        return [
            new TwigFilter('trialDuration', [$this, 'trialDuration'])
        ];
    }

    /**
     * @return array|\Twig_Function[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('userStatus', [$this, 'userStatus'])
        ];
    }

    /**
     * @param User $user
     * @return string
     */
    public function userStatus(User $user): string
    {
        return $this->subscriptionService->getUserStatus($user);
    }

    /**
     * @param User $user
     * @return string
     */
    public function trialDuration(User $user): string
    {
        $seconds = SubscriptionService::MAX_TRIAL_DURATION - $user->getUseDuration();

        if ($seconds < 0) {
            return '0 minute';
        }

        $hours = (int)floor($seconds / 3600);
        $minutes = (int)floor(($seconds % 3600) / 60);

        $labelHours = 'heures';
        $labelMinutes = 'minutes';
        if ($hours <= 1) {
            $labelHours = 'heure';
        }
        if ($minutes <= 1) {
            $labelMinutes = 'minute';
        }

        if (0 === $hours && 0 === $minutes) {
            return "moins d'une minute";
        }

        if (0 === $hours) {
            return $minutes.' '.$labelMinutes;
        }

        if (0 === $minutes) {
            return $hours.' '.$labelHours;
        }

        return $hours.' '.$labelHours.' et '.$minutes.' '.$labelMinutes;
    }

}