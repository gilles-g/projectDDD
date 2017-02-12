<?php

namespace UserIdentity\Domain\Event;

use Prooph\EventSourcing\AggregateChanged;
use UserIdentity\Domain\Model\BusinessInformations;
use UserIdentity\Domain\Model\PublisherId;

class BusinessInformationsUpdated extends AggregateChanged
{
    /**
     * @var PublisherId
     */
    private $publisherId;

    /**
     * @var BusinessInformations
     */
    private $oldBusinessInformations;

    /**
     * @var BusinessInformations
     */
    private $newBusinessInformations;

    public static function withData(
        PublisherId $publisherId,
        BusinessInformations $oldBusinessInformations,
        BusinessInformations $newBusinessInformations
    )
    {
        $event = self::occur(
            (string) $publisherId,
            [
                'old_business_informations' => (string) $oldBusinessInformations->serialize(),
                'new_business_informations' => (string) $newBusinessInformations->serialize(),
            ]
        );

        $event->publisherId = $publisherId;
        $event->oldBusinessInformations = $oldBusinessInformations;
        $event->newBusinessInformations = $newBusinessInformations;

        return $event;
    }

    /**
     * @return PublisherId
     */
    public function publisherId()
    {
        if ($this->publisherId === null) {
            $this->publisherId = PublisherId::fromString($this->aggregateId());
        }

        return $this->publisherId;
    }

    public function oldBusinessInformations()
    {
        if ($this->oldBusinessInformations === null) {
            $this->oldBusinessInformations = BusinessInformations::fromSerialize($this->payload['old_business_informations']);
        }

        return $this->oldBusinessInformations;
    }

    public function newBusinessInformations()
    {
        if ($this->newBusinessInformations === null) {
            $this->newBusinessInformations = BusinessInformations::fromSerialize($this->payload['new_business_informations']);
        }

        return $this->newBusinessInformations;
    }
}