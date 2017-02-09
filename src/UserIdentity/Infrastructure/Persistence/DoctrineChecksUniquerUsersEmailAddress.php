<?php

namespace UserIdentity\Infrastructure\Persistence;

use UserIdentity\Domain\Model\EmailAddress;
use UserIdentity\Domain\Service\ChecksUniqueUsersEmailAddress;
use UserIdentity\Infrastructure\Projection\UserFinder;

class DoctrineChecksUniquerUsersEmailAddress implements ChecksUniqueUsersEmailAddress
{
    private $userFinder;

    public function __construct(UserFinder $userFinder)
    {
        $this->userFinder = $userFinder;
    }

    public function __invoke(EmailAddress $emailAddress)
    {
        return $this->userFinder->findByEmailAddress($emailAddress);
    }
}