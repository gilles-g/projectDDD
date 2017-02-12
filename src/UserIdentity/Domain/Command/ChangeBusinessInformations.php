<?php

namespace UserIdentity\Domain\Command;

use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;
use UserIdentity\Domain\Model\BusinessInformations;
use UserIdentity\Domain\Model\PublisherId;

class ChangeBusinessInformations extends Command implements PayloadConstructable
{
    use PayloadTrait;

    public static function withData($publisherId, $companyName, $siret, $vatNumber)
    {
        return new self(
            [
                'publisher_id' => (string) $publisherId,
                'company_name' => (string) $companyName,
                'siret' => (string) $siret,
                'vat_number' => (string) $vatNumber,
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

    public function businessInformations()
    {
        return BusinessInformations::fromParts(
            $this->payload['company_name'], $this->payload['vat_number'], $this->payload['siret']
        );
    }
}