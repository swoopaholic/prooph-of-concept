<?php
declare(strict_types=1);

namespace Swoopaholic\Domain;

interface Serializable
{
    public function getName(): string;
    public function serialize() : array;
    public static function fromSerializedData($data);
}
