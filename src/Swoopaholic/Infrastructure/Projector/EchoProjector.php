<?php
namespace Swoopaholic\Infrastructure\Projector;

class EchoProjector
{
    public function onTextWasAddedToStream($event)
    {
        echo $event->getText();
    }
}
