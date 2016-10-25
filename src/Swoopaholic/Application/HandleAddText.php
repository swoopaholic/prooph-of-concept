<?php
declare(strict_types=1);

namespace Swoopaholic\Application;

use Assert\Assertion;
use Swoopaholic\Domain\Stream;

final class HandleAddText
{
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(AddText $command)
    {
        // application restricts input to at least 1 character
        // so we place this in the handler and not in domain objects!
        // otherwise, changing application restrictions might cause
        // problems replaying events
        Assertion::minLength(
            (string) $command->getText(), 1, 'Text added to the stream must have at least 1 character'
        );

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
