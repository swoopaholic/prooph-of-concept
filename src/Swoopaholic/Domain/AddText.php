<?php
namespace Swoopaholic\Domain;

class AddText
{
    private $streamId;
    private $text;

    public function __construct($streamId, $text)
    {
        $this->streamId = $streamId;
        $this->text = $text;
    }

    public function getStreamId(): string
    {
        return $this->streamId;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
