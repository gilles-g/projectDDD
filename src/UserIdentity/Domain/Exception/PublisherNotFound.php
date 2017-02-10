<?php

namespace UserIdentity\Domain\Exception;

use UserIdentity\Domain\Model\PublisherId;

class PublisherNotFound extends \InvalidArgumentException
{
    /**
     * @param PublisherId $userId
     * @return PublisherNotFound
     */
    public static function withUserId(PublisherId $userId)
    {
        return new self(sprintf('Publisher with id %s cannot be found.', $userId->toString()));
    }
}