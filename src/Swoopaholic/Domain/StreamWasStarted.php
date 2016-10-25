<?php
declare(strict_types=1);

namespace Swoopaholic\Domain;

final class StreamWasStarted implements Serializable
{
    private $id;

    public function __construct(StreamId $id)
    {
        $this->id = (string) $id;
    }

    public function getId()
    {
        return new StreamId($this->id);
    }

    public function getName(): string
    {
        return 'swoopaholic.text_stream.StreamWasStarted';
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
