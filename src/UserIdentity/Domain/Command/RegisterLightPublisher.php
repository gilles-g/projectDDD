<?php

namespace UserIdentity\Domain\Command;

use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;
use UserIdentity\Domain\Model\EmailAddress;
use UserIdentity\Domain\Model\PublisherId;
use UserIdentity\Domain\Model\UserId;

class RegisterLightPublisher extends Command implements PayloadConstructable
{
    use PayloadTrait;

    public static function withData($publisherId, $userId, $email, $password)
    {
        return new self(
            [
                'publisher_id' => (string) $publisherId,
                'user_d' => (string) $userId,
                'email' => (string) $email,
                'password' => (string) $password,
            ]
        );
    }

    /**
     * @return PublisherId
     */
    public function publisherId()
    {
        return PublisherId::fromString($this->payload['publisher_id']);
    }

    /**
     * @return UserId
     */
    public function userId()
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function emailAddress()
    {
        return EmailAddress::fromString($this->payload['email']);
    }

    public function password()
    {
        return $this->payload['password'];
    }
}