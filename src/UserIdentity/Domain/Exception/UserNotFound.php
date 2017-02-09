<?php

namespace UserIdentity\Domain\Exception;

use UserIdentity\Domain\Model\UserId;

class UserNotFound extends \InvalidArgumentException
{
    /**
     * @param UserId $userId
     * @return UserNotFound
     */
    public static function withUserId(UserId $userId)
    {
        return new self(sprintf('User with id %s cannot be found.', $userId->toString()));
    }
}