<?php
declare(strict_types=1);

namespace Swoopaholic\Application;

use Swoopaholic\Domain\StreamId;
use Swoopaholic\Domain\Text;

final class AddText
{
    private $streamId;
    private $text;

    public function __construct(StreamId $streamId, Text $text)
    {
        $this->streamId = $streamId;
        $this->text = $text;
    }

    public function getStreamId(): StreamId
    {
        return $this->streamId;
    }

    public function getText(): Text
    {
        return $this->text;
    }
}
