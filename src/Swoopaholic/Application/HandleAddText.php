<?php
namespace Swoopaholic\Application;

use Swoopaholic\Domain\AddText;
use Swoopaholic\Domain\Stream;

class HandleAddText
{
    /**
     * @var StreamRepository
     */
    private $repository;

    public function __construct(StreamRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(AddText $command)
    {
        $aggregate = $this->repository->get($command->getStreamId());

        if (is_null($aggregate)) {
            $aggregate = new Stream($command->getStreamId());
        }

        $aggregate->addText($command->getText());

        $this->repository->commit($aggregate);
    }
}
