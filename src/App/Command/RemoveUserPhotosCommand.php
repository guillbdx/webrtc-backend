<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace App\Command;

use App\Entity\User;
use App\Manager\PhotoManager;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RemoveUserPhotosCommand extends Command
{

    /**
     * @var string
     */
    protected static $defaultName = 'illicam:remove-user-photos';

    /**
     * @var PhotoManager
     */
    private $photoManager;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param PhotoManager $photoManager
     * @param UserRepository $userRepository
     */
    public function __construct(
        PhotoManager $photoManager,
        UserRepository $userRepository
    )
    {
        $this->photoManager = $photoManager;
        $this->userRepository = $userRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Removes user photos in database and in AWS s3.')
            ->addArgument('userId', InputArgument::REQUIRED, 'Id of the user to remove photos')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $userId = $input->getArgument('userId');
        $user = $this->userRepository->find($userId);
        if (false === $user instanceof User) {
            throw new InvalidArgumentException(sprintf('%d is not an existing user id.', $userId));
        }
        $this->photoManager->removeUserPhotos($user);

        $io->success('User photos have been removed.');
    }

}