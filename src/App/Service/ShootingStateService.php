<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class ShootingStateService
{

    const ACTIVE    = 'active';

    const INACTIVE  = 'inactive';

    const UNKNOWN   = 'unknown';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    /**
     * @param User $user
     * @return string
     */
    public function getUserShootingState(User $user): string
    {
        if (false === $user->getLastShootAt() instanceof DateTimeImmutable) {
            return self::INACTIVE;
        }
        $now = new DateTimeImmutable();
        $lastShootAge = $now->getTimestamp() - $user->getLastShootAt()->getTimestamp();

        if ($lastShootAge <= 15) {
            return self::ACTIVE;
        }

        return self::UNKNOWN;
    }

    /**
     * @param string $roomId
     * @return string
     */
    public function getUserShootingStateByRoomId(string $roomId): string
    {
        $user = $this->userRepository->findOneBy([
            'roomId' => $roomId
        ]);
        if (false === $user instanceof User) {
            return self::UNKNOWN;
        }

        return $this->getUserShootingState($user);
    }

    /**
     * @param string $roomId
     */
    public function updateLastShootAtByRoomId(string $roomId): void
    {
        $user = $this->userRepository->findOneBy([
            'roomId' => $roomId
        ]);
        if (false === $user instanceof User) {
            return;
        }

        $user->setLastShootAt(new DateTimeImmutable());
        $this->entityManager->flush();
    }

    /**
     * @param string $roomId
     */
    public function clearLastShootAtByRoomId(string $roomId): void
    {
        $user = $this->userRepository->findOneBy([
            'roomId' => $roomId
        ]);
        if (false === $user instanceof User) {
            return;
        }

        /** This ugly statement is needed to have a difference between cache and actual value */
        $user->setLastShootAt(new DateTimeImmutable());
        $this->entityManager->flush();
        /** End ugly statement */

        $user->setLastShootAt(null);
        $this->entityManager->flush();
    }

    /**
     *
     */
    public function clearAllLastShootAts(): void
    {
        $users = $this->userRepository->findAll();
        foreach ($users as $user) {
            $user->setLastShootAt(null);
        }

        $this->entityManager->flush();
    }

}