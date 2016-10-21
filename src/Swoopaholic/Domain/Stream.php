<?php
namespace Swoopaholic\Domain;

final class Stream implements AggregateRoot
{
    private $streamId;
    private $lastRecordedEvents = [];
    private $textStream = [];

    public function getId()
    {
        return $this->streamId;
    }

    public function popRecordedEvents() : array
    {
        $events = $this->lastRecordedEvents;
        $this->lastRecordedEvents = [];
        return $events;
    }

    public static function reconstituteFromHistory(\Iterator $historyEvents)
    {
        $instance = new static();
        $instance->replay($historyEvents);
        return $instance;
    }

    public static function start($streamId)
    {
        $instance = new self();
        $instance->streamId = $streamId;
        $instance->lastRecordedEvents[] = new StreamWasStarted($streamId);
        return $instance;
    }

    public function addText($text)
    {
        $this->recordThat(new TextWasAddedToStream($this->streamId, $text));
        $this->textStream[] = $text;
    }

    public function whenStreamWasStarted(StreamWasStarted $event)
    {
        $this->streamId = $event->getId();
    }

    public function whenTextWasAddedToStream(TextWasAddedToStream $event)
    {
        $this->textStream[] = $event->getText();
    }

    private function recordThat($event)
    {
        $this->lastRecordedEvents[] = $event;
    }

    private function replay(\Iterator $historyEvents)
    {
        foreach ($historyEvents as $pastEvent) {
            $this->apply($pastEvent);
        }
    }

    private function apply($e)
    {
        $handler = 'when' . implode(array_slice(explode('\\', get_class($e)), -1));
        if (! method_exists($this, $handler)) {
            throw new \RuntimeException(sprintf(
                'Missing event handler method %s for aggregate root %s',
                $handler,
                get_class($this)
            ));
        }
        $this->{$handler}($e);
    }
}
