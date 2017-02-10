<?php

namespace UserIdentity\Domain\Query;

class GetPublisherById
{
    /**
     * @var string
     */
    private $publisherId;

    public function __construct($publisherId)
    {
        $this->publisherId = $publisherId;
    }

    public function publisherId()
    {
        return $this->publisherId;
    }
}