<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use DateTimeImmutable;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Email(mode="html5")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="email_check_token", length=255, nullable=true, unique=true)
     */
    private $emailCheckToken;

    /**
     * @var bool
     * @ORM\Column(name="email_checked", type="boolean", nullable=false)
     */
    private $emailChecked;

    /**
     * @var array
     * @ORM\Column(type="array", nullable=false)
     */
    private $roles;

    /**
     * @var string
     */
    private $newPassword;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="password_reset_token", length=255, nullable=true, unique=true)
     */
    private $passwordResetToken;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="room_id", length=255, nullable=false, unique=true)
     */
    private $roomId;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false)
     */
    private $createdAt;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(name="last_shoot_at", type="datetime_immutable", nullable=true)
     */
    private $lastShootAt;

    /**
     * @var bool
     * @ORM\Column(name="alarm_enabled", type="boolean", nullable=false)
     */
    private $alarmEnabled;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(name="last_alarm_at", type="datetime_immutable", nullable=true)
     */
    private $lastAlarmAt;

    /**
     * @var int
     *
     * @ORM\Column(name="use_duration", type="integer", nullable=false)
     */
    private $useDuration;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(name="subscribed_till", type="datetime_immutable", nullable=true)
     */
    private $subscribedTill;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="shooting_token", length=255, nullable=true)
     */
    private $shootingToken;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="alarm_unsubscribe_token", length=255, nullable=false, unique=true)
     */
    private $alarmUnsubscribeToken;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return User
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * @return null|string
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param array $roles
     * @return User
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): ?array
    {
        return $this->roles;
    }

    /**
     *
     */
    public function eraseCredentials()
    {
    }

    /**
     * @return string
     */
    public function getEmailCheckToken(): ?string
    {
        return $this->emailCheckToken;
    }

    /**
     * @param string $emailCheckToken
     * @return User
     */
    public function setEmailCheckToken(?string $emailCheckToken): self
    {
        $this->emailCheckToken = $emailCheckToken;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEmailChecked(): ?bool
    {
        return $this->emailChecked;
    }

    /**
     * @param bool $emailChecked
     * @return User
     */
    public function setEmailChecked(bool $emailChecked): self
    {
        $this->emailChecked = $emailChecked;

        return $this;
    }

    /**
     * @return string
     */
    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    /**
     * @param string $newPassword
     * @return User
     */
    public function setNewPassword(?string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    /**
     * @return string
     */
    public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    /**
     * @param string $passwordResetToken
     * @return User
     */
    public function setPasswordResetToken(?string $passwordResetToken): self
    {
        $this->passwordResetToken = $passwordResetToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoomId(): ?string
    {
        return $this->roomId;
    }

    /**
     * @param string $roomId
     * @return User
     */
    public function setRoomId(string $roomId): self
    {
        $this->roomId = $roomId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->email;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeImmutable $createdAt
     * @return User
     */
    public function setCreatedAt(?DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getLastShootAt(): ?DateTimeImmutable
    {
        return $this->lastShootAt;
    }

    /**
     * @param DateTimeImmutable $lastShootAt
     * @return User
     */
    public function setLastShootAt(?DateTimeImmutable $lastShootAt): self
    {
        $this->lastShootAt = $lastShootAt;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAlarmEnabled(): ?bool
    {
        return $this->alarmEnabled;
    }

    /**
     * @param bool $alarmEnabled
     * @return User
     */
    public function setAlarmEnabled(bool $alarmEnabled): self
    {
        $this->alarmEnabled = $alarmEnabled;

        return $this;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getLastAlarmAt(): ?DateTimeImmutable
    {
        return $this->lastAlarmAt;
    }

    /**
     * @param DateTimeImmutable $lastAlarmAt
     * @return User
     */
    public function setLastAlarmAt(?DateTimeImmutable $lastAlarmAt): self
    {
        $this->lastAlarmAt = $lastAlarmAt;

        return $this;
    }

    /**
     * @return int
     */
    public function getUseDuration(): ?int
    {
        return $this->useDuration;
    }

    /**
     * @param int $useDuration
     * @return User
     */
    public function setUseDuration(?int $useDuration): self
    {
        $this->useDuration = $useDuration;

        return $this;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getSubscribedTill(): ?DateTimeImmutable
    {
        return $this->subscribedTill;
    }

    /**
     * @param DateTimeImmutable $subscribedTill
     * @return User
     */
    public function setSubscribedTill(?DateTimeImmutable $subscribedTill): self
    {
        $this->subscribedTill = $subscribedTill;

        return $this;
    }

    /**
     * @return string
     */
    public function getShootingToken(): ?string
    {
        return $this->shootingToken;
    }

    /**
     * @param string $shootingToken
     * @return User
     */
    public function setShootingToken(?string $shootingToken): self
    {
        $this->shootingToken = $shootingToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlarmUnsubscribeToken(): ?string
    {
        return $this->alarmUnsubscribeToken;
    }

    /**
     * @param string $alarmUnsubscribeToken
     * @return User
     */
    public function setAlarmUnsubscribeToken(string $alarmUnsubscribeToken): self
    {
        $this->alarmUnsubscribeToken = $alarmUnsubscribeToken;

        return $this;
    }

}