<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace Components\Signaling\Model;

class Message
{

    const PING                      = 'PING';
    const PONG                      = 'PONG';
    const SHOOTER_INTRODUCTION      = 'SHOOTER_INTRODUCTION';
    const WATCHER_INTRODUCTION      = 'WATCHER_INTRODUCTION';
    const OFFER                     = 'OFFER';
    const ANSWER                    = 'ANSWER';
    const ICE                       = 'ICE';
    const SHOOTER_NOT_EXISTS        = 'SHOOTER_NOT_EXISTS';
    const SHOOTER_SEEMS_NOT_EXISTS  = 'SHOOTER_SEEMS_NOT_EXISTS';
    const START_SIGNALING           = 'START_SIGNALING';

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $roomId;

    /**
     * @var mixed
     */
    private $body;

    /**
     * Message constructor.
     * @param string $type
     * @param string $roomId
     * @param mixed $body
     */
    public function __construct(
        string $type = null,
        string $roomId = null,
        $body = null
    )
    {
        $this->type = $type;
        $this->roomId = $roomId;
        $this->body = $body;
    }
    
    /**
     * @param string $json
     */
    public function populateFromJson(string $json): void
    {
        $data = json_decode($json, true);
        
        $this->type     = $data['type'];
        $this->roomId   = $data['roomId'];
        $this->body     = $data['body'];
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getRoomId(): ?string
    {
        return $this->roomId;
    }

    /**
     * @param string $roomId
     */
    public function setRoomId(string $roomId): void
    {
        $this->roomId = $roomId;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body): void
    {
        $this->body = $body;
    }

    /**
     * @return array
     */
    public function getAsArray(): array
    {
        return [
            'type'      => $this->getType(),
            'roomId'    => $this->getRoomId(),
            'body'      => $this->getBody()
        ];
    }

    /**
     * @return string
     */
    public function getAsJson(): string 
    {
        $data = $this->getAsArray();
        return json_encode($data);
    }
    
}