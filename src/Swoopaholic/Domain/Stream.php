<?php
declare(strict_types=1);

namespace Swoopaholic\Domain;

final class Stream implements AggregateRoot
{
    use AggregateRootTrait;

    private $streamId;
    private $textStream = [];

    public function getId()
    {
        return new StreamId($this->streamId);
    }

    public static function start(StreamId $streamId)
    {
        $instance = new self();
        $instance->streamId = (string) $streamId;
        $instance->recordThat(new StreamWasStarted($streamId));
        return $instance;
    }

    public function addText($text)
    {
        $this->recordThat(new TextWasAddedToStream($this->streamId, $text));
    }

    public function whenStreamWasStarted(StreamWasStarted $event)
    {
        $this->streamId = $event->getId();
    }

    public function whenTextWasAddedToStream(TextWasAddedToStream $event)
    {
        $this->textStream[] = $event->getText();
    }
}
