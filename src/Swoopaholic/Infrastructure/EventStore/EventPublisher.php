<?php
declare(strict_types=1);

namespace Swoopaholic\Infrastructure\EventStore;

use Prooph\Common\Event\ActionEvent;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Plugin\Plugin;
use Prooph\ServiceBus\EventBus;
use Swoopaholic\Domain\Serializable;

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

    private function convert(Message $message)
    {
        /** @var Serializable $class */
        $class = $message->metadata()['class'];
        return $class::fromSerializedData($message->payload());
    }
}
