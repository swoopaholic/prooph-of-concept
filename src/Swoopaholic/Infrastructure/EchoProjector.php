<?php
namespace Swoopaholic\Infrastructure;

class EchoProjector
{
    public function onTextWasAddedToStream($event)
    {
        echo $event->getText();
    }
}
