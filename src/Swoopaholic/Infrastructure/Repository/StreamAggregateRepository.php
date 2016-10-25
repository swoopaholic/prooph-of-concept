<?php
declare(strict_types=1);

namespace Swoopaholic\Infrastructure\Repository;

use Prooph\EventStore\Aggregate\AggregateRepository;
use Swoopaholic\Application\Repository as RepositoryInterface;

final class StreamAggregateRepository extends AggregateRepository implements RepositoryInterface
{
    public function get($id)
    {
        return $this->getAggregateRoot((string) $id);
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
