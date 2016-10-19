<?php
namespace Swoopaholic\Domain;

class Stream implements AggregateRoot
{
    private $streamId;

    private $lastRecordedEvents = [];

    private $textStream = [];

    public function __construct($streamId)
    {
        $this->streamId = $streamId;
    }

    public function getId()
    {
        return $this->streamId;
    }

    public function addText($text)
    {
        $this->recordThat(new TextWasAddedToStream($this->streamId, $text));
        $this->textStream[] = $text;
    }

    public function getRecordedEvents()
    {
        return $this->lastRecordedEvents;
    }

    public function clearRecordedEvents()
    {
        $this->lastRecordedEvents = [];
    }

    private function recordThat($event)
    {
        $this->lastRecordedEvents[] = $event;
    }
}
