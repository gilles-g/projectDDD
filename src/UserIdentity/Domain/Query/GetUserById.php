<?php

namespace UserIdentity\Domain\Query;

class GetUserById
{
    /**
     * @var string
     */
    private $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function userId()
    {
        return $this->userId;
    }
}