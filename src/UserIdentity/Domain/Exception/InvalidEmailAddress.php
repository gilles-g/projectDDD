<?php

namespace UserIdentity\Domain\Exception;

class InvalidEmailAddress extends \InvalidArgumentException
{
    /**
     * @param string $msg
     * @return InvalidEmailAddress
     */
    public static function reason($msg)
    {
        return new self('Invalid email because ' . (string) $msg);
    }
}