<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace Components\FixturesGeneration\Service;

use App\Entity\Photo;
use App\Entity\User;
use App\Factory\PhotoFactory;
use App\Factory\UserFactory;
use App\Manager\PhotoManager;
use App\Manager\UserManager;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;
use Faker\Factory;
use DateTimeImmutable;

class FixturesGenerator
{

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var PhotoFactory
     */
    private $photoFactory;

    /**
     * @var PhotoManager
     */
    private $photoManager;

    /**
     * @var Factory
     */
    private $faker;

    /**
     * @var Finder
     */
    private $finder;

    /**
     * @param UserFactory $userFactory
     * @param UserManager $userManager
     * @param PhotoFactory $photoFactory
     * @param PhotoManager $photoManager
     */
    public function __construct(
        UserFactory $userFactory,
        UserManager $userManager,
        PhotoFactory $photoFactory,
        PhotoManager $photoManager
    )
    {
        $this->userFactory = $userFactory;
        $this->userManager = $userManager;
        $this->photoFactory = $photoFactory;
        $this->photoManager = $photoManager;
        $this->faker = Factory::create('fr_FR');
        $this->finder = new Finder();
    }

    /**
     *
     */
    public function generate(): void
    {
        $usersFixturesFile = __DIR__.'/../Resources/fixtures/users.yaml';
        $usersDatas = Yaml::parse(file_get_contents($usersFixturesFile));
        foreach ($usersDatas as $userData) {
            $this->generateUser($userData);
        }
    }

    /**
     * @param array $userData
     */
    private function generateUser(array $userData): void
    {
        $user = $this->userFactory->init();
        $user->setEmail($userData['email']);
        $user->setPassword($userData['password']);
        $this->userManager->signup($user);
        $this->userManager->validateEmail($user);
        if (true === $userData['generatePhotos']) {
            $this->generateSetsOfPhotos($user);
        }
    }

    /**
     * @param User $user
     */
    private function generateSetsOfPhotos(User $user): void
    {
        for ($i = 0; $i < 1; $i++) {
            $this->generateOneSetOfPhotos($user);
        }
    }

    /**
     * @param User $user
     */
    private function generateOneSetOfPhotos(User $user): void
    {
        $this->finder->files()->in(__DIR__.'/../Resources/fixtures/photos');
        foreach ($this->finder as $file) {
            $this->generateOnePhoto($user, $file);
        }
    }

    /**
     * @param User $user
     * @param SplFileInfo $file
     */
    private function generateOnePhoto(User $user, SplFileInfo $file): void
    {

        $nameChunks = explode('-', $file->getFilename());
        if (2 === count($nameChunks) && 'before.jpg' === $nameChunks[1]) {
            return;
        }
        if (2 === count($nameChunks) && 'after.jpg' === $nameChunks[1]) {
            $this->generateMismatch($user, $file);
            return;
        }

        $photo = $this->createPhotoFromFile($user, $file);
        $this->photoManager->save($photo);
    }

    /**
     * @param User $user
     * @param SplFileInfo $file
     * @return Photo
     */
    private function createPhotoFromFile(User $user, SplFileInfo $file): Photo
    {
        $base64 = base64_encode($file->getContents());
        $photo = $this->photoFactory->init();
        $photo->setUser($user);
        $createdAt = $this->faker->dateTimeThisMonth();
        $photo->setCreatedAt(DateTimeImmutable::createFromMutable($createdAt));
        $photo->setBase64($base64);

        return $photo;
    }

    /**
     * @param string $prefix
     * @return SplFileInfo
     */
    private function findBeforeFile(string $prefix): SplFileInfo
    {
        $files = $this->finder->files()->in(__DIR__.'/../Resources/fixtures/photos')->name($prefix.'-before.jpg');
        return $files->getIterator()->current();
    }

    /**
     * @param User $user
     * @param SplFileInfo $fileAfter
     */
    private function generateMismatch(User $user, SplFileInfo $fileAfter)
    {
        $nameChunks = explode('-', $fileAfter->getFilename());
        $beforeFile = $this->findBeforeFile($nameChunks[0]);
        $photoBefore = $this->createPhotoFromFile($user, $beforeFile);
        $photoAfter = $this->createPhotoFromFile($user, $fileAfter);
        $mismatch = $this->faker->numberBetween(300, 2000);
        $this->photoManager->saveMismatch($photoBefore, $photoAfter, $mismatch);
    }

}