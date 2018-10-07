<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="photo")
 * @ORM\Entity(repositoryClass="App\Repository\PhotoRepository")
 */
class Photo
{

    const REGULAR   = 0;
    const BEFORE    = 1;
    const AFTER     = 2;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"browse"})
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @Assert\NotBlank()
     */
    private $user;

    /**
     * @var string
     */
    private $base64;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false)
     */
    private $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $type;

    /**
     * @var Photo
     *
     * @ORM\OneToOne(targetEntity="Photo", cascade={"all"})
     * @ORM\JoinColumn(name="mismatched_photo_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * @Groups({"browse"})
     */
    private $mismatchedPhoto;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"browse"})
     */
    private $mismatch;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="secret", length=255, nullable=true)
     */
    private $secret;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return Photo
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getBase64(): ?string
    {
        return $this->base64;
    }

    /**
     * @param string $base64
     * @return Photo
     */
    public function setBase64(string $base64): self
    {
        $this->base64 = $base64;

        return $this;
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
     * @return Photo
     */
    public function setCreatedAt(?DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return int
     *
     * @Groups({"browse"})
     */
    public function getTimestamp(): int
    {
        return $this->getCreatedAt()->getTimestamp();
    }

    /**
     * @return int
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return Photo
     */
    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Photo
     */
    public function getMismatchedPhoto(): ?Photo
    {
        return $this->mismatchedPhoto;
    }

    /**
     * @param Photo $mismatchedPhoto
     * @return Photo
     */
    public function setMismatchedPhoto(?Photo $mismatchedPhoto): self
    {
        $this->mismatchedPhoto = $mismatchedPhoto;

        return $this;
    }

    /**
     * @return int
     */
    public function getMismatch(): ?int
    {
        return $this->mismatch;
    }

    /**
     * @param int $mismatch
     * @return Photo
     */
    public function setMismatch(?int $mismatch): self
    {
        $this->mismatch = $mismatch;

        return $this;
    }

    /**
     * @return string
     */
    public function getSecret(): ?string
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     * @return Photo
     */
    public function setSecret(?string $secret): self
    {
        $this->secret = $secret;

        return $this;
    }

}