<?php
namespace Swoopaholic\Infrastructure\EventStore;

use Prooph\Common\Event\ActionEvent;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Plugin\Plugin;
use Prooph\ServiceBus\EventBus;

final class EventPublisher implements Plugin
{
    private $eventBus;

    public function __construct(EventBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    public function setUp(EventStore $eventStore)
    {
        $eventStore->getActionEventEmitter()
            ->attachListener('commit.post', [$this, 'onEventStoreCommitPost']);
    }

    public function onEventStoreCommitPost(ActionEvent $actionEvent)
    {
        $recordedEvents = $actionEvent->getParam('recordedEvents', []);

        foreach ($recordedEvents as $recordedEvent) {
            $this->eventBus->dispatch($this->convert($recordedEvent));
        }
    }

    private function convert($message)
    {
        $class = $message->messageName();
        return $class::deserialize($message->payload());
    }
}
