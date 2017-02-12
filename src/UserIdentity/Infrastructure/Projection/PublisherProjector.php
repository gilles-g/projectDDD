<?php

namespace UserIdentity\Infrastructure\Projection;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use UserIdentity\Domain\Event\BusinessInformationsUpdated;
use UserIdentity\Domain\Event\LightPublisherWasRegistered;
use UserIdentity\Domain\Model\Publisher;

class PublisherProjector
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

    public function onLightPublisherWasRegistered(LightPublisherWasRegistered $event)
    {
        $publisher = Publisher::createwhenLightPublisherWasRegistered($event);

        $this->entityManager()->persist($publisher);
        $this->entityManager()->flush();
    }

    public function onBusinessInformationsUpdated(BusinessInformationsUpdated $event)
    {
        $publisher = $this->managerRegistry->getRepository(Publisher::class)->find($event->publisherId()->toString());
        $publisher->whenBusinessInformationsUpdated($event);

        $this->entityManager()->flush();
    }
}