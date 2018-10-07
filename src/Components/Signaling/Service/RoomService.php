<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace Components\Signaling\Service;

use Components\Signaling\Model\Room;
use Ratchet\ConnectionInterface;

class RoomService
{

    /**
     * @var Room[]
     */
    private $rooms;

    /**
     * RoomService constructor.
     */
    public function __construct()
    {
        $this->rooms = [];
    }

    /**
     * @param string $roomId
     * @return Room
     */
    private function getRoom(string $roomId): Room
    {
        if (false === array_key_exists($roomId, $this->rooms)) {
            $this->rooms[$roomId] = new Room();
        }
        
        return $this->rooms[$roomId];
    }

    /**
     * @param string $roomId
     * @param ConnectionInterface $shooter
     */
    public function setShooterInRoom(string $roomId, ConnectionInterface $shooter): void
    {
        $room = $this->getRoom($roomId);
        $room->setShooter($shooter);
    }

    /**
     * @param string $roomId
     * @param ConnectionInterface $watcher
     */
    public function setWatcherInRoom(string $roomId, ConnectionInterface $watcher): void
    {
        $room = $this->getRoom($roomId);
        $room->setWatcher($watcher);
    }

    /**
     * @param ConnectionInterface $connection
     * @return string
     */
    public function getShooterRoomByConnection(ConnectionInterface $connection): ?string
    {
        foreach ($this->rooms as $roomId => $room) {
            if (true === $room->isShooterOfThis($connection)) {
                return $roomId;
            }
        }

        return null;
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function removeConnection(ConnectionInterface $connection): void
    {
        foreach ($this->rooms as $room) {
            $room->removeConnectionIfExists($connection);
        }
    }

    /**
     * @param string $roomId
     * @param ConnectionInterface $connection
     * @return null|ConnectionInterface
     */
    public function getOppositeConnectionInRoom(
        string $roomId,
        ConnectionInterface $connection
    ): ?ConnectionInterface
    {
        $room = $this->getRoom($roomId);
        return $room->getOppositeConnection($connection);
    }

    /**
     * @param string $roomId
     */
    public function setShooterPingAtNowInRoom(string $roomId)
    {
        $room = $this->getRoom($roomId);
        $room->setShooterPingAtNow();
    }

    /**
     * @param string $roomId
     * @return bool
     */
    public function watcherExistsInRoom(string $roomId): bool
    {
        $room = $this->getRoom($roomId);

        return $room->watcherExists();
    }

    /**
     * @return array
     */
    public function dump(): array
    {
        $dumps = [];
        foreach ($this->rooms as $key => $room) {
            $dumps[$key] = $room->dump();
        }

        return $dumps;
    }

}