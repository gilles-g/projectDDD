<?php

namespace UserIdentity\Domain\Exception;

use UserIdentity\Domain\Model\UserId;

class UserAlreadyExists extends \InvalidArgumentException
{
    /**
     * @param UserId $userId
     * @return UserAlreadyExists
     */
    public static function withUserId(UserId $userId)
    {
        return new self(sprintf('User with id %s already exists.', $userId->toString()));
    }
}
