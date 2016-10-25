<?php
declare(strict_types=1);

namespace Swoopaholic\Infrastructure\Projector;

use Swoopaholic\Domain\TextWasAddedToStream;

final class EchoProjector
{
    public function onTextWasAddedToStream(TextWasAddedToStream $event)
    {
        echo $event->getText();
    }
}
