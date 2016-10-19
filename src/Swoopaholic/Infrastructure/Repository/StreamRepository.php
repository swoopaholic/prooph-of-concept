<?php
namespace Swoopaholic\Infrastructure\Repository;

use Prooph\ServiceBus\EventBus;
use Swoopaholic\Application\StreamRepository as StreamRepositoryInterface;
use Swoopaholic\Domain\AggregateRoot;

class StreamRepository implements StreamRepositoryInterface
{
    private $aggregates = [];
    /**
     * @var EventBus
     */
    private $eventBus;

    public function __construct(EventBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    public function commit(AggregateRoot $aggregate)
    {
        // todo... replace with event store
        foreach ($aggregate->getRecordedEvents() as $event) {
            $this->eventBus->dispatch($event);
        }
        $aggregate->clearRecordedEvents();

        $this->aggregates[$aggregate->getId()] = $aggregate;
    }

    public function get($id)
    {
        return isset($this->aggregates[$id]) ? $this->aggregates[$id] : null;
    }
}
