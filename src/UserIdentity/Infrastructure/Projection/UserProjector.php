<?php

namespace UserIdentity\Infrastructure\Projection;

use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use UserIdentity\Domain\Event\UserWasRegistered;
use UserIdentity\Domain\Model\User;

class UserProjector
{
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @return EntityManager
     */
    private function entityManager()
    {
        return $this->managerRegistry
            ->getManagerForClass(User::class);
    }

    public function onUserWasRegistered(UserWasRegistered $event)
    {
        $user = User::createWhenUserWasRegistered($event);

        $this->entityManager()->persist($user);
        $this->entityManager()->flush();
    }
}