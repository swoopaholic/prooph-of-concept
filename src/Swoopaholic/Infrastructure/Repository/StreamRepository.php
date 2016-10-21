<?php
namespace Swoopaholic\Infrastructure\Repository;

use Prooph\EventStore\Aggregate\AggregateRepository;
use Swoopaholic\Application\StreamRepository as StreamRepositoryInterface;

class StreamRepository extends AggregateRepository  implements StreamRepositoryInterface
{
    public function get($id)
    {
        return $this->getAggregateRoot($id);
    }

    public function add($stream)
    {
        $this->eventStore->beginTransaction();
        $this->addAggregateRoot($stream);
        $this->eventStore->commit();
    }

    public function commit()
    {
        $this->eventStore->beginTransaction();
        $this->addPendingEventsToStream();
        $this->eventStore->commit();
    }
}
