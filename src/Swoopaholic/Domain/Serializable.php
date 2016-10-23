<?php
namespace Swoopaholic\Domain;

interface Serializable
{
    public function serialize() : array;
    public static function fromSerializedData($data);
}
