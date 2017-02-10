<?php

namespace UserIdentity\Infrastructure\Projection;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use UserIdentity\Domain\Exception\PublisherNotFound;
use UserIdentity\Domain\Model\Publisher;
use UserIdentity\Domain\Model\PublisherId;

class PublisherFinder
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
            ->getManagerForClass(Publisher::class);
    }

    protected function entityRepository()
    {
        return $this->managerRegistry
            ->getRepository(Publisher::class);
    }

    /**
     * @param PublisherId $publisherId
     * @return Publisher
     */
    public function byId(PublisherId $publisherId)
    {
        $publisher = $this->entityRepository()->find($publisherId);

        if (!$publisher instanceof Publisher) {
            throw new PublisherNotFound();
        }

        return $publisher;
    }
}