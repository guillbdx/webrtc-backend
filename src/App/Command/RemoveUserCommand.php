<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace App\Command;

use App\Entity\User;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RemoveUserCommand extends Command
{

    /**
     * @var string
     */
    protected static $defaultName = 'dilcam:remove-user';

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @param UserRepository $userRepository
     * @param UserManager $userManager
     */
    public function __construct(
        UserRepository $userRepository,
        UserManager $userManager
    )
    {
        $this->userRepository = $userRepository;
        $this->userManager = $userManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Removes user.')
            ->addArgument('userId', InputArgument::REQUIRED, 'Id of the user to remove')
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
        $this->userManager->remove($user);
        $io->success('User has been removed.');
    }

}