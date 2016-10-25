<?php
declare(strict_types=1);

namespace Swoopaholic\Domain;

use Assert\Assertion;

final class StreamId
{
    private $id;

    public function __construct(string $id)
    {
        Assertion::uuid($id);
        $this->id = $id;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
