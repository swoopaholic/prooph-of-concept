<?php
namespace Swoopaholic\Infrastructure\EventStore;

use Prooph\Common\Messaging\Message as MessageInterface;
use Rhumsaa\Uuid\Uuid;

class Message implements MessageInterface
{
    private $uuid;
    private $name;
    private $payload;
    private $createdAt;
    private $type = Message::TYPE_EVENT;
    private $metadata = [];
    private $version = '1';

    public function __construct($name, array $payload = [])
    {
        $this->uuid = Uuid::uuid4();
        $this->name = $name;
        $this->payload = $payload;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function messageName()
    {
        return $this->name;
    }

    public function payload()
    {
        return $this->payload;
    }

    public function messageType()
    {
        return $this->type;
    }

    public function uuid()
    {
        return $this->uuid;
    }

    public function version()
    {
        return $this->version;
    }

    public function createdAt()
    {
        return $this->createdAt;
    }

    public function metadata()
    {
        return $this->metadata;
    }

    public function withVersion($version)
    {
        $new = new self($this->name, $this->metadata);
        $new->uuid = $this->uuid;
        $new->version = $version;
        return $new;
    }

    public function withMetadata(array $metadata)
    {
        $new = new self($this->name, $this->metadata);
        $new->uuid = $this->uuid;
        $new->metadata = $metadata;
        return $new;
    }

    public function withAddedMetadata($key, $value)
    {
        $new = new self($this->name, $this->payload);
        $new->uuid = $this->uuid;
        $new->metadata = array_merge($this->metadata, [$key => $value]);
        return $new;
    }
}
