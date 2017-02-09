<?php

namespace Common\Domain\Model;

use Rhumsaa\Uuid\Uuid;

abstract class IdentifiedDomainObject
{
    protected $id;

    /**
     * {@inheritdoc}
     */
    public static function generate()
    {
        return self::fromString(
            (string) Uuid::uuid4()
        );
    }

    /**
     * @param $string
     *
     * @return static
     */
    public static function fromString($string)
    {
        $id = new static();
        $id->id = $string;

        return $id;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->id;
    }

    public function toString()
    {
        return $this->__toString();
    }
}