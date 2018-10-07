<?php

namespace Components\Signaling\Service;

use App\Service\ShootingStateService;
use Components\Signaling\Model\Message;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use React\EventLoop\LoopInterface;

class SignalingService implements MessageComponentInterface
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var RoomService
     */
    private $roomService;

    /**
     * @var ShootingStateService
     */
    private $shootingStateService;

    /**
     * @var TurnGetter
     */
    private $turnGetter;

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @param LoggerInterface $logger
     * @param RoomService $roomService
     * @param ShootingStateService $shootingStateService
     * @param TurnGetter $turnGetter
     */
    public function __construct(
        LoggerInterface $logger,
        RoomService $roomService,
        ShootingStateService $shootingStateService,
        TurnGetter $turnGetter
    )
    {
        $this->logger = $logger;
        $this->roomService = $roomService;
        $this->shootingStateService = $shootingStateService;
        $this->turnGetter = $turnGetter;
    }

    /**
     * @param LoopInterface $loop
     */
    public function setLoop(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function onOpen(ConnectionInterface $connection)
    {
        $this->logger->info('New connection', ['reourceId' => $connection->resourceId]);
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function onClose(ConnectionInterface $connection)
    {
        $this->logger->info('Disconnection', ['reourceId' => $connection->resourceId]);

        $shooterRoomId = $this->roomService->getShooterRoomByConnection($connection);
        if (is_string($shooterRoomId)) {
            $this->shootingStateService->clearLastShootAtByRoomId($shooterRoomId);
        }

        $this->roomService->removeConnection($connection);
        $this->logRooms();
    }

    /**
     * @param ConnectionInterface $connection
     * @param \Exception $error
     */
    public function onError(ConnectionInterface $connection, \Exception $error)
    {
        $this->logger->error('Error', [
            'reourceId' => $connection->resourceId,
            'error' => $error->getMessage()
        ]);
    }

    /**
     * @param ConnectionInterface $from
     * @param string $jsonMessage
     */
    public function onMessage(ConnectionInterface $from, $jsonMessage)
    {
        $message = new Message();
        $message->populateFromJson($jsonMessage);

        $this->logMessage($from, $message);

        if (Message::PING === $message->getType()) {
            $this->pong($from, $message->getRoomId());
            return;
        }

        if (Message::SHOOTER_INTRODUCTION === $message->getType()) {
            $this->introduceShooter($from, $message->getRoomId());
            return;
        }

        if (Message::WATCHER_INTRODUCTION === $message->getType()) {
            $this->introduceWatcher($from, $message->getRoomId());
            return;
        }

        if (Message::OFFER === $message->getType()
            || Message::ANSWER === $message->getType()) {
            $this->forwardMessageToOppositeConnection($from, $message->getRoomId(), $jsonMessage);
        }

        if (Message::ICE === $message->getType()) {
            $this->forwardIceCandidate($from, $message, $jsonMessage);
        }
    }

    /**
     * @param ConnectionInterface $from
     * @param Message $message
     */
    private function logMessage(ConnectionInterface $from, Message $message)
    {
        $level = Logger::INFO;
        if (Message::PING === $message->getType()) {
            $level = Logger::DEBUG;
        }

        $loggedBody = $message->getBody();
        if (true === is_array($loggedBody) && true === isset($loggedBody['sdp'])) {
            $loggedBody['sdp'] = 'SDP content';
        }

        $this->logger->log($level, $message->getType(), [
            'reourceId'     => $from->resourceId,
            'roomId'        => $message->getRoomId(),
            'body'          => $loggedBody,
        ]);
    }

    /**
     *
     */
    private function logRooms(): void
    {
        $this->logger->notice('Rooms', $this->roomService->dump());
    }

    /**
     * @param ConnectionInterface $connection
     * @param string $roomId
     */
    private function pong(ConnectionInterface $connection, string $roomId)
    {
        $this->roomService->setShooterPingAtNowInRoom($roomId);
        $this->shootingStateService->updateLastShootAtByRoomId($roomId);
        $this->sendMessage($connection, Message::PONG);
    }

    /**
     * @param ConnectionInterface $shooter
     * @param string $roomId
     * @throws \Exception
     */
    private function introduceShooter(ConnectionInterface $shooter, string $roomId)
    {
        $this->roomService->setShooterInRoom($roomId, $shooter);
        $this->shootingStateService->updateLastShootAtByRoomId($roomId);
        $this->logRooms();
        if (true === $this->roomService->watcherExistsInRoom($roomId)) {
            $iceServers = $this->turnGetter->getTurnServers();
            $this->sendMessage($shooter, Message::START_SIGNALING, $roomId, $iceServers);
        }
    }

    /**
     * @param ConnectionInterface $watcher
     * @param string $roomId
     */
    private function introduceWatcher(ConnectionInterface $watcher, string $roomId)
    {
        $this->roomService->setWatcherInRoom($roomId, $watcher);
        $shootingState = $this->shootingStateService->getUserShootingStateByRoomId($roomId);

        if (ShootingStateService::ACTIVE === $shootingState) {
            $shooter = $this->roomService->getOppositeConnectionInRoom($roomId, $watcher);
            $iceServers = $this->turnGetter->getTurnServers();
            $this->sendMessage($shooter, Message::START_SIGNALING, $roomId, $iceServers);
            $this->logRooms();
            return;
        }

        if (ShootingStateService::INACTIVE === $shootingState) {
            $this->sendMessage($watcher, Message::SHOOTER_NOT_EXISTS);
            return;
        }

        $this->sendMessage($watcher, Message::SHOOTER_SEEMS_NOT_EXISTS);
    }

    /**
     * @param ConnectionInterface $connection
     * @param string $roomId
     * @param string $jsonMessage
     */
    private function forwardMessageToOppositeConnection(ConnectionInterface $connection, string $roomId, string $jsonMessage)
    {
        $otherConnection = $this->roomService->getOppositeConnectionInRoom($roomId, $connection);
        if (true === $otherConnection instanceof ConnectionInterface) {
            $otherConnection->send($jsonMessage);
        }
    }

    /**
     * @param ConnectionInterface $connection
     * @param string $type
     * @param string|null $roomId
     * @param null $body
     */
    private function sendMessage(
        ConnectionInterface $connection,
        string $type,
        string $roomId = null,
        $body = null
    ): void
    {
        $message = new Message($type, $roomId, $body);
        $connection->send($message->getAsJson());
    }

    /**
     * @param ConnectionInterface $connection
     * @param Message $message
     * @param string $jsonMessage
     */
    private function forwardIceCandidate(
        ConnectionInterface $connection,
        Message $message,
        string $jsonMessage
    ): void
    {
        if ($this->isRelayIceCandidate($message->getBody())) {
            $this->logger->info('ICE will be sent in 4 seconds because relay');
            $this->loop->addTimer(4, function() use ($connection, $message, $jsonMessage) {
                $this->forwardMessageToOppositeConnection($connection, $message->getRoomId(), $jsonMessage);
            });
            return;
        }
        $this->forwardMessageToOppositeConnection($connection, $message->getRoomId(), $jsonMessage);
    }

    /**
     * @param $iceMessageBody
     * @return bool
     */
    private function isRelayIceCandidate($iceMessageBody): bool
    {
        return false !== strpos($iceMessageBody['ice']['candidate'], 'typ relay');
    }

}