<?php

namespace Project\Domain\Model;

use Common\Domain\Model\IdentifiedDomainObject;
use Ramsey\Uuid\Uuid;

class ProjectId extends IdentifiedDomainObject
{
    /**
     * @return self
     */
    public static function generate()
    {
        return self::fromString(
            (string) Uuid::uuid4()
        );
    }
}