<?php

namespace UserIdentity\Infrastructure\Persistence;

use Prooph\EventStore\Aggregate\AggregateRepository;
use UserIdentity\Domain\Model\Publisher;
use UserIdentity\Domain\Model\PublisherId;
use UserIdentity\Domain\Model\PublisherRepository;

class EventStorePublisherRepository extends AggregateRepository implements PublisherRepository
{
    public function add(Publisher $publisher)
    {
        $this->addAggregateRoot($publisher);
    }

    public function remove(Publisher $publisher)
    {
        // TODO: Implement remove() method.
    }

    public function publisherOfId(PublisherId $publisherId)
    {
        return $this->getAggregateRoot($publisherId->toString());
    }

    /**
     * @return PublisherId
     */
    public function nextIdentity()
    {
        // TODO: Implement nextIdentity() method.
    }
}