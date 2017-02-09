<?php

namespace UserIdentity\Domain\Service;

use UserIdentity\Domain\Model\EmailAddress;

interface ChecksUniqueUsersEmailAddress
{
    public function __invoke(EmailAddress $emailAddress);
}