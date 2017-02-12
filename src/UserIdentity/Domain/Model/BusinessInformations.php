<?php

namespace UserIdentity\Domain\Model;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\SerializerBuilder;

/**
 * @ORM\Embeddable()
 */
class BusinessInformations
{
    /**
     * @var string
     * @Serializer\Type("string")
     *
     * @ORM\Column(name="company_name", type="string", nullable=true)
     */
    protected $companyName;

    /**
     * @var string
     * @Serializer\Type("string")
     *
     * @ORM\Column(name="vat_number", type="string", nullable=true)
     */
    protected $vatNumber;

    /**
     * @var string
     * @Serializer\Type("string")
     *
     * @ORM\Column(name="business_siret", type="string", nullable=true)
     */
    protected $siret;

    private function __construct($companyName, $vatNumber, $siret)
    {
        $this->companyName = $companyName;
        $this->vatNumber = $vatNumber;
        $this->siret = $siret;
    }

    public static function fromParts($companyName, $vatNumber, $siret)
    {
        return new self($companyName, $vatNumber, $siret);
    }

    public static function fromSerialize($serialized)
    {
        return self::deserialize($serialized);
    }

    public function sameValueAs(BusinessInformations $businessInformations)
    {
        return $businessInformations == $this;
    }

    public function serialize()
    {
        return SerializerBuilder::create()->build()->serialize($this, 'json');
    }

    /**
     * @param $data
     * @return BusinessInformations
     */
    public static function deserialize($data)
    {
        return SerializerBuilder::create()->build()->deserialize($data, self::class, 'json');
    }
}