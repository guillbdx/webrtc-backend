<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace App\Model;

use App\Entity\User;
use App\Service\SubscriptionService;
use DateTimeImmutable;
use Exception;

class Subscription
{

    /**
     * @var User
     */
    private $user;

    /**
     * @var DateTimeImmutable
     */
    private $startAt;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @param int $quantity
     * @param User $user
     */
    public function __construct(int $quantity, User $user)
    {
        $this->checkQuantity($quantity);
        $this->quantity = $quantity;
        $this->user = $user;
        $this->setStartAt();
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return Subscription
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @throws Exception
     */
    private function setStartAt(): void
    {
        $now = new DateTimeImmutable();
        if (false === $this->user->getSubscribedTill() instanceof DateTimeImmutable) {
            $this->startAt = $now;
            return;
        }

        if ($this->user->getSubscribedTill() < $now) {
            $this->startAt = $now;
            return;
        }

        $this->startAt = $this->user->getSubscribedTill();
    }

    /**
     * @return DateTimeImmutable
     */
    public function getStartAt(): DateTimeImmutable
    {
        return $this->startAt;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return Subscription
     */
    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->quantity * SubscriptionService::MONTHLY_PRICE;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getEndAt(): DateTimeImmutable
    {
        $startAtTimestamp = strtotime($this->startAt->format('Y-m-d H:i:s'));
        $endAtDate = date('Y-m-d H:i:s', strtotime("+$this->quantity months", $startAtTimestamp));
        $endAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $endAtDate);

        return $endAt;
    }

    /**
     * @param int $quantity
     * @throws Exception
     */
    private function checkQuantity(int $quantity): void
    {
        if (false === is_int($quantity)) {
            throw new Exception("Invalid quantity");
        }
        if ($quantity < 1 || $quantity > 24) {
            throw new Exception("Invalid quantity");
        }
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Abonnement '.$this->quantity.' mois';
    }

}