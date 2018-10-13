<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace App\Manager;

use App\Entity\Photo;
use App\Entity\User;
use App\Repository\PhotoRepository;
use App\Service\PhotoNormalizer;
use App\Service\TokenGenerator;
use Aws\S3\S3Client;
use Components\Emailing\AppMailer;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use DateTimeImmutable;

class PhotoManager
{

    public const MIN_MISMATCH_FOR_ALARMING  = 750;

    public const MIN_ALARM_INTERVAL         = 600;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var S3Client
     */
    private $s3Client;

    /**
     * @var string
     */
    private $bucket;

    /**
     * @var PhotoRepository
     */
    private $photoRepository;

    /**
     * @var PhotoNormalizer
     */
    private $photoNormalizer;

    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    /**
     * @var AppMailer
     */
    private $appMailer;

    /**
     * @param EntityManagerInterface $entityManager
     * @param string $awsKeyId
     * @param string $awsKeySecret
     * @param string $host
     * @param PhotoRepository $photoRepository
     * @param PhotoNormalizer $photoNormalizer
     * @param TokenGenerator $tokenGenerator
     * @param AppMailer $appMailer
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        string $awsKeyId,
        string $awsKeySecret,
        string $host,
        PhotoRepository $photoRepository,
        PhotoNormalizer $photoNormalizer,
        TokenGenerator $tokenGenerator,
        AppMailer $appMailer
    )
    {
        $this->entityManager = $entityManager;
        $this->s3Client = S3Client::factory([
            'version' => 'latest',
            'region'  => 'eu-west-3',
            'credentials' => [
                'key'    => $awsKeyId,
                'secret' => $awsKeySecret,
            ]
        ]);
        $this->bucket = str_replace('.', '-', $host) . '-photos';
        $this->photoRepository = $photoRepository;
        $this->photoNormalizer = $photoNormalizer;
        $this->tokenGenerator = $tokenGenerator;
        $this->appMailer = $appMailer;
    }

    /**
     * @param Photo $photo
     * @param int $type
     */
    public function save(
        Photo $photo,
        int $type = Photo::REGULAR
    ): void
    {
        $this->photoNormalizer->normalize($photo);
        if (false === $photo->getCreatedAt() instanceof DateTimeImmutable) {
            $photo->setCreatedAt(new DateTimeImmutable());
        }
        $photo->setType($type);
        $this->entityManager->persist($photo);
        $this->entityManager->flush();
        $this->s3Client->putObject(array(
            'Bucket'    => $this->bucket,
            'Key'       => $photo->getId(),
            'Body'      => base64_decode($photo->getBase64()),
        ));
    }

    /**
     * @param User $user
     */
    public function removeUserPhotos(User $user): void
    {
        $photos = $this->photoRepository->findBy([
            'user' => $user
        ]);

        $this->removePhotos($photos);
    }

    /**
     * @param array $photos
     */
    public function removePhotos(array $photos): void
    {
        $chunks = array_chunk($photos, 1000);
        foreach ($chunks as $chunk) {
            $photosIds = array_map(function(Photo $photo) {
                return ['Key' => $photo->getId()];
            }, $chunk);

            $this->s3Client->deleteObjects([
                'Bucket'  => $this->bucket,
                'Delete' => [
                    'Objects' => $photosIds
                ],
            ]);
        }

        foreach ($photos as $photo) {
            $this->entityManager->remove($photo);
        }
        $this->entityManager->flush();
    }

    /**
     * @param Photo $photo
     * @return string
     */
    public function getPhotoBlob(Photo $photo): ?string
    {
        try {
            $object = $this->s3Client->getObject([
                'Bucket'    => $this->bucket,
                'Key'       => $photo->getId()
            ]);
            return $object['Body'];
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * @param Photo $photoBefore
     * @param Photo $photoAfter
     * @param int $mismatch
     */
    public function saveMismatch(Photo $photoBefore, Photo $photoAfter, int $mismatch): void
    {
        $photoAfter->setMismatchedPhoto($photoBefore);
        $photoAfter->setMismatch($mismatch);
        $photoAfter->setSecret($this->tokenGenerator->generate(12));
        $this->save($photoBefore, Photo::BEFORE);
        $this->save($photoAfter, Photo::AFTER);
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

        if ($mismatch < self::MIN_MISMATCH_FOR_ALARMING) {
            return;
        }

        $lastAlarmAt = $user->getLastAlarmAt();
        if (
            true === $lastAlarmAt instanceof DateTimeImmutable
            && time() - $lastAlarmAt->getTimestamp() < self::MIN_ALARM_INTERVAL
        ) {
            return;
        }

        $this->appMailer->sendAlarm($user, $photo);
        $user->setLastAlarmAt(new \DateTimeImmutable());
        $this->entityManager->flush();
    }

}