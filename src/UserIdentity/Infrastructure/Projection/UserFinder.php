<?php

namespace UserIdentity\Infrastructure\Projection;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use UserIdentity\Domain\Exception\UserNotFound;
use UserIdentity\Domain\Model\EmailAddress;
use UserIdentity\Domain\Model\User;
use UserIdentity\Domain\Model\UserId;

class UserFinder
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

    protected function entityRepository()
    {
        return $this->managerRegistry
            ->getRepository(User::class);
    }

    /**
     * @param UserId $userId
     * @return User
     */
    public function byId(UserId $userId)
    {
        $user = $this->entityRepository()->find($userId);

        if (!$user instanceof User) {
            throw new UserNotFound();
        }

        return $user;
    }

    /**
     * @param EmailAddress $emailAddress
     * @return UserId|null
     */
    public function findByEmailAddress(EmailAddress $emailAddress)
    {
        try {
            UserId::fromString($this->entityManager()
                ->createQueryBuilder()
                ->select('u.userId')
                ->from(User::class, 'u')
                ->where('u.emailAddress.email = :email')
                ->setParameter('email', $emailAddress->toString())
                ->getQuery()
                ->getSingleScalarResult());
        } catch (NonUniqueResultException $e) {
            return null;
        } catch (NoResultException $e) {
            return null;
        }
    }
}