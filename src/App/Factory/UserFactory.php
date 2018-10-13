<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace App\Factory;

use App\Entity\User;
use App\Service\TokenGenerator;
use DateTimeImmutable;

class UserFactory
{

    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    /**
     * UserFactory constructor.
     * @param TokenGenerator $tokenGenerator
     */
    public function __construct(
        TokenGenerator $tokenGenerator
    )
    {
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @return User
     */
    public function init(): User
    {
        $user = new User();
        $user->setEmailCheckToken($this->tokenGenerator->generate());
        $user->setEmailChecked(false);
        $user->setRoles(['ROLE_USER']);
        $user->setRoomId($this->tokenGenerator->generate());
        $user->setCreatedAt(new DateTimeImmutable());
        $user->setAlarmEnabled(true);
        $user->setUseDuration(0);
        $user->setAlarmUnsubscribeToken($this->tokenGenerator->generate());

        return $user;
    }
}