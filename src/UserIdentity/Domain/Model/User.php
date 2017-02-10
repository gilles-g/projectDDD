<?php

namespace UserIdentity\Domain\Model;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping as ORM;
use Prooph\EventSourcing\AggregateRoot;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use UserIdentity\Domain\Event\UserWasRegistered;

/**
 * Class User
 * @package UserIdentity\Domain\Model
 * @author Gauthier Gilles <g.gauthier@lexik.fr>
 *
 * @Entity()
 * @ORM\Table(name="user_identity_user")
 */
class User extends AggregateRoot implements UserInterface, EquatableInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     *
     * @var UserId
     */
    private $userId;

    /**
     * @var FullName
     *
     * @ORM\Embedded(class="FullName", columnPrefix="fullname_")
     */
    protected $fullName;

    /**
     * @var EmailAddress
     *
     * @ORM\Embedded(class="EmailAddress", columnPrefix="email_address_")
     */
    protected $emailAddress;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var array
     *
     * @ORM\Column(type="json_array")
     */
    private $roles = [];

    /**
     * @return UserId
     */
    public function getUserId()
    {
        return UserId::fromString($this->userId);
    }

    public static function registerWithData(UserId $userId, EmailAddress $emailAddress, $password, $roles)
    {
        $self = new self();

        $self->recordThat(UserWasRegistered::withData($userId, $emailAddress, $password, $roles));

        return $self;
    }

    public function whenUserWasRegistered(UserWasRegistered $event)
    {
        $this->userId = $event->userId();
        $this->emailAddress = $event->emailAddress();
        $this->username = $event->emailAddress()->toString();
        $this->password = $event->password();
        $this->roles = [$event->roles()];
    }

    public static function createWhenUserWasRegistered(UserWasRegistered $event)
    {
        $user = new self();
        $user->userId = (string) $event->userId();
        $user->emailAddress = $event->emailAddress();
        $user->username = $event->emailAddress()->toString();
        $user->password = $event->password();
        $user->roles = [$event->roles()];

        return $user;
    }

    public function getRoles()
    {
        $roles = $this->roles;

        // guarantees that a user always has at least one role for security
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }
        return array_unique($roles);
    }

    /**
     * @return EmailAddress
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
    }

    public function isEqualTo(UserInterface $user)
    {
        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    public function __toString()
    {
        return $this->aggregateId();
    }

    /**
     * @return string representation of the unique identifier of the aggregate root
     */
    protected function aggregateId()
    {
        return (string) $this->userId;
    }
}