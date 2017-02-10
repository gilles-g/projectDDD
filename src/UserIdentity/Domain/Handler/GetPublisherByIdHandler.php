<?php

namespace UserIdentity\Domain\Handler;

use React\Promise\Deferred;
use UserIdentity\Domain\Model\PublisherId;
use UserIdentity\Domain\Query\GetPublisherById;
use UserIdentity\Infrastructure\Projection\PublisherFinder;

class GetPublisherByIdHandler
{
    /**
     * @var PublisherFinder
     */
    private $publisherFinder;

    public function __construct(PublisherFinder $publisherFinder)
    {
        $this->publisherFinder = $publisherFinder;
    }

    public function __invoke(GetPublisherById $query, Deferred $deferred = null)
    {
        $publisher = $this->publisherFinder->byId(PublisherId::fromString($query->publisherId()));

        if (null === $deferred) {
            return $publisher;
        }

        return $deferred->resolve($publisher);
    }
}