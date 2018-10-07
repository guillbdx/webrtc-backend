<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace App\Command;

use App\Entity\Photo;
use App\Entity\User;
use App\Manager\PhotoManager;
use App\Repository\PhotoRepository;
use App\Repository\UserRepository;
use App\Service\SubscriptionService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use DateTimeImmutable;

class RemoveOldPhotosCommand extends Command
{

    /**
     * @var string
     */
    public static $defaultName = 'dilcam:remove-old-photos';

    /**
     * @var  UserRepository
     */
    private $userRepository;

    /**
     * @var PhotoRepository
     */
    private $photoRepository;

    /**
     * @var PhotoManager
     */
    private $photoManager;

    /**
     * @param UserRepository $userRepository
     * @param PhotoRepository $photoRepository
     * @param PhotoManager $photoManager
     */
    public function __construct(
        UserRepository $userRepository,
        PhotoRepository $photoRepository,
        PhotoManager $photoManager
    )
    {
        $this->userRepository = $userRepository;
        $this->photoRepository = $photoRepository;
        $this->photoManager = $photoManager;

        parent::__construct();
    }

    /**
     *
     */
    public function configure()
    {
        $this
            ->setDescription('Removes all photos of each user.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $users = $this->userRepository->findAll();
        foreach ($users as $user) {
            $this->removeUserOldPhotos($user);
        }
    }

    /**
     * @param User $user
     */
    private function removeUserOldPhotos(User $user): void
    {
        $count = $this->photoRepository->countRegularPhotosByUser($user);
        if ($count <= SubscriptionService::MAX_ARCHIVED_PHOTOS) {
            return;
        }

        $dateLimit = $this->getDateLimit($user, $count);
        $photosToRemove = $this->photoRepository->findAllPhotosByUserBefore($user, $dateLimit);

        $this->photoManager->removePhotos($photosToRemove);
    }

    /**
     * @param User $user
     * @param int $count
     * @return DateTimeImmutable
     */
    public function getDateLimit(User $user, int $count): DateTimeImmutable
    {
        $regularPhotos = $this->photoRepository->findRegularPhotosByUser($user);
        $photosToKeep = array_slice($regularPhotos, $count - SubscriptionService::MAX_ARCHIVED_PHOTOS);
        /** @var Photo $oldestPhotoToKeep */
        $oldestPhotoToKeep = $photosToKeep[0];

        return $oldestPhotoToKeep->getCreatedAt();
    }

}