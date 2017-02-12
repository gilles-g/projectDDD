<?php

namespace UserIdentity\Domain\Model;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Project\Domain\Model\Project;
use Doctrine\ORM\Mapping as ORM;
use Prooph\EventSourcing\AggregateRoot;
use UserIdentity\Domain\Event\BusinessInformationsUpdated;
use UserIdentity\Domain\Event\LightPublisherWasRegistered;

/**
 * Class Publisher
 * @package UserIdentity\Domain\Model
 * @author Gauthier Gilles <g.gauthier@lexik.fr>
 *
 * @Entity()
 * @Table(name="user_identity_publisher")
 */
class Publisher extends AggregateRoot
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     *
     * @var PublisherId
     */
    private $publisherId;

    /**
     * @ORM\Column(type="string")
     *
     * @var UserId
     */
    private $userId;

    /**
     * @var BusinessInformations
     *
     * @ORM\Embedded(class="UserIdentity\Domain\Model\BusinessInformations", columnPrefix="business_informations")
     */
    private $businessInformations;

    /**
     * @var Project[]
     */
    private $projects;

    public static function registerLightPublisher(PublisherId $publisherId, UserId $userId)
    {
        $self = new self();

        $self->recordThat(LightPublisherWasRegistered::withData($publisherId, $userId));

        return $self;
    }

    /**
     * @param LightPublisherWasRegistered $event
     */
    public function whenLightPublisherWasRegistered(LightPublisherWasRegistered $event)
    {
        $this->publisherId = $event->publisherId();
        $this->userId = $event->userId();
    }

    /**
     * @param LightPublisherWasRegistered $event
     * @return Publisher
     */
    public static function createwhenLightPublisherWasRegistered(LightPublisherWasRegistered $event)
    {
        $publisher = new self();
        $publisher->userId = $event->userId();
        $publisher->publisherId = $event->publisherId();

        return $publisher;
    }

    public function changeBusinessInformations(BusinessInformations $businessInformations)
    {
        $oldBusinessInformations = $this->businessInformations?: BusinessInformations::fromParts('-', '-', '-');

        $this->businessInformations = $businessInformations;

        $this->recordThat(BusinessInformationsUpdated::withData(
            $this->publisherId,
            $oldBusinessInformations,
            $businessInformations)
        );
    }

    public function whenBusinessInformationsUpdated(BusinessInformationsUpdated $event)
    {
        $this->businessInformations = $event->newBusinessInformations();
    }

    /**
     * @return PublisherId
     */
    public function getPublisherId()
    {
        return PublisherId::fromString($this->publisherId);
    }

    /**
     * @return UserId
     */
    public function getUserId()
    {
        return UserId::fromString($this->userId);
    }

    /**
     * @return \Project\Domain\Model\Project[]
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * @return BusinessInformations
     */
    public function getBusinessInformations()
    {
        return $this->businessInformations;
    }

    /**
     * @param \Project\Domain\Model\Project[] $projects
     */
    public function setProjects($projects)
    {
        $this->projects = $projects;
    }

    public function __toString()
    {
        return $this->aggregateId();
    }

    /**
     * @return string representation of the unique identifier of the aggregate root
     */
    protected function aggregateId()
    {
        return (string) $this->publisherId;
    }
}