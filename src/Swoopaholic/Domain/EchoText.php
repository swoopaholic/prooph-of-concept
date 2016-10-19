<?php
namespace Swoopaholic\Domain;

class EchoText
{
    private $text;
    private $streamId;

    public function __construct($streamId, $text)
    {
        $this->text = $text;
        $this->streamId = $streamId;
    }

    /**
     * @return mixed
     */
    public function getStreamId()
    {
        return $this->streamId;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }
}
