<?php

namespace UserIdentity\Infrastructure\Persistence;

use Prooph\EventStore\Aggregate\AggregateRepository;
use UserIdentity\Domain\Model\User;
use UserIdentity\Domain\Model\UserId;
use UserIdentity\Domain\Model\UserRepository;

class EventStoreUserRepository extends AggregateRepository implements UserRepository
{
    public function add(User $user)
    {
        $this->addAggregateRoot($user);
    }

    public function userOfId(UserId $userId)
    {
        return $this->getAggregateRoot($userId->toString());
    }

    public function remove(User $user)
    {
        // TODO: Implement remove() method.
    }

    public function userWithUsername($username)
    {
        // TODO: Implement userWithUsername() method.
    }

    public function all()
    {
        // TODO: Implement all() method.
    }

    /**
     * @return UserId
     */
    public function nextIdentity()
    {
        // TODO: Implement nextIdentity() method.
    }
}