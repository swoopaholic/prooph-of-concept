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

    public static function fromSerializedData($data)
    {
        $ref = new \ReflectionClass(self::class);
        $object = $ref->newInstanceWithoutConstructor();
        $object->id = $data['id'];
        return $object;
    }
}
