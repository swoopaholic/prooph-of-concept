<?php
namespace Swoopaholic\Domain;

class StreamWasStarted implements Serializable
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function serialize() : array
    {
        return [
            'id' => $this->id
        ];
    }

    public static function deserialize($data)
    {
        return new self($data['id']);
    }
}
