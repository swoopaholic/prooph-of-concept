<?php
namespace Swoopaholic\Application;

use Swoopaholic\Domain\AggregateRoot;

interface StreamRepository
{
    /**
     * @param $id
     * @return AggregateRoot
     */
    public function get($id);

    /**
     * @param AggregateRoot $aggregate
     * @return mixed
     */
    public function commit(AggregateRoot $aggregate);
}
