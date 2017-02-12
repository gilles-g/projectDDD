<?php

namespace UserIdentity\Infrastructure\Rx;

use Prooph\ServiceBus\QueryBus;
use Rx\React\Promise;

class QueryObservable
{
    /**
     * @var QueryBus
     */
    private $queryBus;

    public function __construct(QueryBus $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    public function dispatch($query)
    {
        return Promise::toObservable($this->queryBus->dispatch($query));
    }
}