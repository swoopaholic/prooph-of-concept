<?php
namespace Swoopaholic\Domain;

class TextWasAddedToStream implements Serializable
{
    private $id;
    private $text;

    public function __construct($id, $text)
    {
        $this->id = $id;
        $this->text = $text;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getText()
    {
        return $this->text;
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
