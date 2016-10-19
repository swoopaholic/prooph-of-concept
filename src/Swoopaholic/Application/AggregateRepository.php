<?php
namespace Swoopaholic\Application;

class AggregateRepository
{
    private $aggregates = [];

    public function commit($aggregate)
    {
        $this->aggregates[$aggregate->getId()] = $aggregate;
    }

    public function find($id)
    {
        return isset($this->aggregates[$id]) ? $this->aggregates[$id] : null;
    }
}
