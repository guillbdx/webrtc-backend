<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace App\Command;

use App\Manager\UserManager;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ClearDatabaseCommand extends Command
{

    /**
     * @var string
     */
    protected static $defaultName = 'illicam:clear-database';

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
            ->setDescription('Clears all database.')
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
        $users = $this->userRepository->findAll();
        foreach ($users as $user) {
            $this->userManager->remove($user);
        }

        $io->success('Database cleared.');
    }

}