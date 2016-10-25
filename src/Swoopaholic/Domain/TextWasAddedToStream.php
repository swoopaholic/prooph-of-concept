<?php
declare(strict_types=1);

namespace Swoopaholic\Domain;

final class TextWasAddedToStream implements Serializable
{
    private $id;
    private $text;

    public function __construct(StreamId $id, Text $text)
    {
        $this->id = (string) $id;
        $this->text = (string) $text;
    }

    public function getId()
    {
        return new StreamId($this->id);
    }

    public function getText()
    {
        return new Text($this->text);
    }

    public function getName(): string
    {
        return 'swoopaholic.text_stream.TextWasAddedToStream';
    }

    public function serialize() : array
    {
        return [
            'id' => $this->id,
            'text' => $this->text
        ];
    }

    public static function fromSerializedData($data)
    {
        $ref = new \ReflectionClass(self::class);
        $object = $ref->newInstanceWithoutConstructor();
        $object->id = $data['id'];
        $object->text = $data['text'];
        return $object;
    }
}
