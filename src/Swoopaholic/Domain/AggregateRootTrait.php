<?php
declare(strict_types=1);

namespace Swoopaholic\Domain;

trait AggregateRootTrait
{
    private $lastRecordedEvents = [];

    public function popRecordedEvents() : array
    {
        return array_splice($this->lastRecordedEvents, 0);
    }

    public static function reconstituteFromHistory(\Iterator $historyEvents)
    {
        $instance = new static();
        $instance->replay($historyEvents);
        return $instance;
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
