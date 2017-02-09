<?php

namespace UserIdentity\Domain\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class FullName
 * @package UserIdentity\Domain\Model
 * @author Gauthier Gilles <g.gauthier@lexik.fr>
 *
 * @ORM\Embeddable()
 */
class FullName
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $lastName;

    public static function fromParts($firstName, $lastName)
    {
        $fullName = new static();
        $fullName->firstName = $firstName;
        $fullName->lastName = $lastName;

        return $fullName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }
}