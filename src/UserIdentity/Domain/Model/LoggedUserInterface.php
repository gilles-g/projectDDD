<?php

namespace UserIdentity\Domain\Model;

interface LoggedUserInterface
{
    public function __toString();

    /**
     * @return UserId
     */
    public function getUserId();

    /**
     * @return PublisherId
     */
    public function getPublisherId();
}