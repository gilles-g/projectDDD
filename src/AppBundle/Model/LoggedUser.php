<?php

namespace AppBundle\Model;

use UserIdentity\Domain\Model\PublisherId;
use UserIdentity\Domain\Model\UserId;

class LoggedUser
{
    private $userId;

    private $publisherId;

    public function __construct(UserId $userId, PublisherId $publisherId)
    {
        $this->userId = $userId;
        $this->publisherId = $publisherId;
    }

    public function __toString()
    {
        return sprintf('%s', $this->userId->toString());
    }

    /**
     * @return UserId
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return PublisherId
     */
    public function getPublisherId()
    {
        return $this->publisherId;
    }
}