<?php

namespace Components\Signaling\Command;

use App\Service\ShootingStateService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Components\Signaling\Service\SignalingService;

class SignalingCommand extends Command
{

    /**
     * @var string
     */
    protected static $defaultName = 'dilcam:signaling';

    /**
     * @var SignalingService
     */
    private $signalingService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ShootingStateService
     */
    private $shootingStateService;

    /**
     * @param LoggerInterface $logger
     * @param SignalingService $signalingService
     * @param ShootingStateService $shootingStateService
     */
    public function __construct(
        LoggerInterface $logger,
        SignalingService $signalingService,
        ShootingStateService $shootingStateService
    )
    {
        $this->logger = $logger;
        $this->signalingService = $signalingService;
        $this->shootingStateService = $shootingStateService;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Starts signaling service.')
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


        $this->shootingStateService->clearAllLastShootAts();

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    $this->signalingService
                )
            ),
            3000
        );
        $io->text('Signaling service running. Pending for socket connections.');

        $this->signalingService->setLoop($server->loop);

        $server->run();
    }
}
