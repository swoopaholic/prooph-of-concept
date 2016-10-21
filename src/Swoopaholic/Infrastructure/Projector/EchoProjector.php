<?php
namespace Swoopaholic\Infrastructure\Projector;

use Swoopaholic\Domain\TextWasAddedToStream;

class EchoProjector
{
    public function onTextWasAddedToStream(TextWasAddedToStream $event)
    {
        echo $event->getText();
    }
}
