<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace Components\Signaling\Model;

use Ratchet\ConnectionInterface;
use DateTimeImmutable;

class Room
{

    /**
     * @var ConnectionInterface
     */
    private $shooter;

    /**
     * @var ConnectionInterface
     */
    private $watcher;

    /**
     * @var DateTimeImmutable
     */
    private $shooterPingAt;

    /**
     * @param ConnectionInterface $shooter
     * @return Room
     */
    public function setShooter(ConnectionInterface $shooter): Room
    {
        $this->shooter = $shooter;
        $this->setShooterPingAtNow();
        return $this;
    }

    /**
     * @return ConnectionInterface
     */
    public function getShooter(): ?ConnectionInterface
    {
        return $this->shooter;
    }

    /**
     * @param ConnectionInterface $watcher
     * @return Room
     */
    public function setWatcher(ConnectionInterface $watcher): Room
    {
        $this->watcher = $watcher;
        return $this;
    }

    /**
     * @return ConnectionInterface
     */
    public function getWatcher(): ?ConnectionInterface
    {
        return $this->watcher;
    }

    /**
     * @return Room
     */
    public function setShooterPingAtNow(): Room
    {
        $this->shooterPingAt = new DateTimeImmutable();
        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getShooterPingAt(): ?DateTimeImmutable
    {
        return $this->shooterPingAt;
    }

    /**
     * @param ConnectionInterface $connection
     * @return bool
     */
    public function isShooterOfThis(ConnectionInterface $connection): bool
    {
        return $this->shooter instanceof ConnectionInterface
            && $connection->resourceId === $this->shooter->resourceId;
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function removeConnectionIfExists(ConnectionInterface $connection)
    {
        if ($this->shooter instanceof ConnectionInterface
            && $connection->resourceId === $this->shooter->resourceId) {
            $this->shooterPingAt = null;
            $this->shooter = null;
        }
        if ($this->watcher instanceof ConnectionInterface
            && $connection->resourceId === $this->watcher->resourceId) {
            $this->watcher = null;
        }
    }

    /**
     * @param ConnectionInterface $connection
     * @return null|ConnectionInterface
     */
    public function getOppositeConnection(ConnectionInterface $connection): ?ConnectionInterface
    {
        if ($this->shooter instanceof ConnectionInterface
            && $connection->resourceId === $this->shooter->resourceId) {
            return $this->getWatcher();
        }
        if ($this->watcher instanceof ConnectionInterface
            && $connection->resourceId === $this->watcher->resourceId) {
            return $this->getShooter();
        }

        return null;
    }

    /**
     * @return bool
     */
    public function watcherExists(): bool
    {
        return $this->watcher instanceof ConnectionInterface;
    }

    /**
     * @return array
     */
    public function dump(): array
    {
        $dump = [];
        if ($this->getShooter() instanceof ConnectionInterface) {
            $dump['shooter'] = $this->getShooter()->resourceId;
        }
        if ($this->getWatcher() instanceof ConnectionInterface) {
            $dump['watcher'] = $this->getWatcher()->resourceId;
        }
        if ($this->shooterPingAt instanceof DateTimeImmutable) {
            $dump['shooterPingAt'] = $this->shooterPingAt->format('Y-m-d H:i:s');
        }

        return $dump;
    }

}