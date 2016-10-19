<?php
namespace Swoopaholic\Domain;

interface AggregateRoot
{
    public function getRecordedEvents();

    public function clearRecordedEvents();
}
