<?php
declare(strict_types=1);

namespace Swoopaholic\Domain;

final class Text
{
    private $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function __toString()
    {
        return $this->text;
    }
}
