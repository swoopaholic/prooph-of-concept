<?php
namespace Swoopaholic\Application;

use Prooph\ServiceBus\EventBus;
use Swoopaholic\Domain\EchoText;
use Swoopaholic\Domain\Stream;

class HandleEchoText
{
    /**
     * @var AggregateRepository
     */
    private $repository;
    /**
     * @var EventBus
     */
    private $eventBus;

    public function __construct(AggregateRepository $repository, EventBus $eventBus)
    {
        $this->repository = $repository;
        $this->eventBus = $eventBus;
    }

    public function handle(EchoText $command)
    {
        $aggregate = $this->repository->find($command->getStreamId());

        if (is_null($aggregate)) {
            $aggregate = new Stream($command->getStreamId());
        }

        $aggregate->addText($command->getText());

        foreach ($aggregate->getRecordedEvents() as $event) {
            $this->eventBus->dispatch($event);
        }
    }
}
