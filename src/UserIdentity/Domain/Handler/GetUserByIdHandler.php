<?php

namespace UserIdentity\Domain\Handler;

use React\Promise\Deferred;
use Rx\Observable;
use UserIdentity\Domain\Model\UserId;
use UserIdentity\Domain\Query\GetUserById;
use UserIdentity\Infrastructure\Projection\UserFinder;

class GetUserByIdHandler
{
    /**
     * @var UserFinder
     */
    private $userFinder;

    public function __construct(UserFinder $userFinder)
    {
        $this->userFinder = $userFinder;
    }

    public function __invoke(GetUserById $query, Deferred $deferred = null)
    {
        $user = $this->userFinder->byId(UserId::fromString($query->userId()));

        if (null === $deferred) {
            return $user;
        }

        return $deferred->resolve($user);
    }
}