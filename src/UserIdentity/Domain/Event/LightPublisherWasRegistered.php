<?php

namespace UserIdentity\Domain\Event;

use Prooph\EventSourcing\AggregateChanged;
use UserIdentity\Domain\Model\PublisherId;
use UserIdentity\Domain\Model\UserId;

class LightPublisherWasRegistered extends AggregateChanged
{
    /**
     * @var PublisherId
     */
    private $publisherId;

    /**
     * @var UserId
     */
    private $userId;

    public static function withData(PublisherId $publisherId, UserId $userId)
    {
        $event = self::occur(
            (string) $publisherId,
            [
                'user_id' => (string) $userId,
            ]
        );

        $event->publisherId = $publisherId;
        $event->userId = $userId;

        return $event;
    }

    public function publisherId()
    {
        if ($this->publisherId === null) {
            $this->publisherId = PublisherId::fromString($this->aggregateId());
        }

        return $this->publisherId;
    }

    public function userId()
    {
        if ($this->userId === null) {
            $this->userId = UserId::fromString($this->aggregateId());
        }

        return $this->userId;
    }
}