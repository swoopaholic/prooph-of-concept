<?php
namespace Swoopaholic\Application;

use Swoopaholic\Domain\AddText;
use Swoopaholic\Domain\Stream;

class HandleAddText
{
    private $repository;

    public function __construct(StreamRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(AddText $command)
    {
        $stream = $this->getStream($command->getStreamId());

        $stream->addText($command->getText());
        $this->repository->commit();
    }

    private function getStream($id): Stream
    {
        $stream = $this->repository->get($id);

        if (is_null($stream)) {
            $this->repository->add(Stream::start($id));
            $stream = $this->repository->get($id);
        }

        return $stream;
    }
}
