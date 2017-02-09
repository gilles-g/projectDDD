<?php

namespace UserIdentity\Domain\Model;

use UserIdentity\Domain\Exception\InvalidEmailAddress;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class EmailAddress
 * @package UserIdentity\Domain\Model
 * @author Gauthier Gilles <g.gauthier@lexik.fr>
 *
 * @ORM\Embeddable()
 */
class EmailAddress
{
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $email;

    /**
     * @param string $email
     * @return EmailAddress
     */
    public static function fromString(string $email)
    {
        return new self($email);
    }

    /**
     * @param string $emailAddress
     */
    private function __construct(string $emailAddress)
    {
        if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            throw InvalidEmailAddress::reason('filter_var returned false');
        }

        $this->email = $emailAddress;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->email;
    }

    /**
     * @param EmailAddress $other
     * @return bool
     */
    public function sameValueAs(EmailAddress $other)
    {
        return $this->toString() === $other->toString();
    }
}