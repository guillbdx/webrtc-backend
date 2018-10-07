<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace Components\FixturesGeneration\Command;

use Components\FixturesGeneration\Service\FixturesGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FixturesCommand extends Command
{

    /**
     * @var FixturesGenerator
     */
    private $fixturesGenerator;

    /**
     * @var string
     */
    protected static $defaultName = 'illicam:fixtures';

    /**
     * @param FixturesGenerator $fixturesGenerator
     */
    public function __construct(FixturesGenerator $fixturesGenerator)
    {
        $this->fixturesGenerator = $fixturesGenerator;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Generates fixtures.')
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
        $this->fixturesGenerator->generate();
        $io->success('Fixtures generated.');
    }

}