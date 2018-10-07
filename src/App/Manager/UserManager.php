<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace App\Manager;

use App\Entity\Photo;
use App\Entity\User;
use Components\Emailing\AppMailer;
use App\Service\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use DateTimeImmutable;

class UserManager
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AppMailer
     */
    private $appMailer;

    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    /**
     * @var PhotoManager
     */
    private $photoManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TokenStorageInterface $tokenStorage
     * @param AppMailer $appMailer
     * @param TokenGenerator $tokenGenerator
     * @param PhotoManager $photoManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        TokenStorageInterface $tokenStorage,
        AppMailer $appMailer,
        TokenGenerator $tokenGenerator,
        PhotoManager $photoManager
    )
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenStorage = $tokenStorage;
        $this->appMailer = $appMailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->photoManager = $photoManager;
    }

    /**
     * @param User $user
     */
    public function log(User $user): void
    {
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->tokenStorage->setToken($token);
    }

    /**
     * @param User $user
     */
    public function changePassword(User $user): void
    {
        $encodedPassword = $this->passwordEncoder->encodePassword($user, $user->getNewPassword());
        $user->setPassword($encodedPassword);
        $user->setPasswordResetToken(null);

        $this->entityManager->flush();
    }

    /**
     * @param User $user
     */
    public function signup(User $user): void
    {
        $encodedPassword = $this->passwordEncoder->encodePassword($user, $user->getPassword());
        $user->setPassword($encodedPassword);

        $this->log($user);

        $this->appMailer->sendEmailCheckToken($user);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * @param User $user
     */
    public function validateEmail(User $user): void
    {
        $user->setEmailChecked(true);
        $user->setEmailCheckToken(null);
        $this->entityManager->flush();
    }

    /**
     * @param User $user
     */
    public function resendEmailCheckToken(User $user): void
    {
        $this->appMailer->sendEmailCheckToken($user);
    }

    /**
     * @param User $user
     */
    public function requestPasswordReset(User $user)
    {
        $passwordResetToken = $this->tokenGenerator->generate();
        $user->setPasswordResetToken($passwordResetToken);
        $this->entityManager->flush();
        $this->appMailer->sendPasswordResetToken($user);
    }

    /**
     * @param User $user
     */
    public function remove(User $user): void
    {
        $this->photoManager->removeUserPhotos($user);
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    /**
     * @param User $user
     */
    public function enableAlarm(User $user): void
    {
        $user->setAlarmEnabled(true);
        $this->entityManager->flush();
    }

    /**
     * @param User $user
     */
    public function disableAlarm(User $user): void
    {
        $user->setAlarmEnabled(false);
        $this->entityManager->flush();
    }

    /**
     * @param Photo $photo
     * @param int $mismatch
     */
    public function sendAlarmIfNeed(Photo $photo, int $mismatch): void
    {
        $user = $photo->getUser();

        if (false === $user->isAlarmEnabled()) {
            return;
        }

        if ($mismatch < 500) {
            return;
        }

        $lastAlarmAt = $user->getLastAlarmAt();
        if (true === $lastAlarmAt instanceof DateTimeImmutable && time() - $lastAlarmAt->getTimestamp() < 600) {
            return;
        }

        $this->appMailer->sendAlarm($user, $photo);
        $user->setLastAlarmAt(new \DateTimeImmutable());
        $this->entityManager->flush();
    }

}