<?php

namespace Project\Domain\Model;

use Doctrine\ORM\Mapping as ORM;
use Common\Domain\Model\IdentifiedDomainObject;
use UserIdentity\Domain\Model\PublisherId;

class Project extends IdentifiedDomainObject
{
    /**
     * @var ProjectId
     *
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    protected $projectId;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var PublisherId
     */
    private $publisherId;

    /**
     * Project constructor.
     * @param ProjectId $projectId
     * @param PublisherId $publisherId
     * @param $name
     */
    public function __construct(ProjectId $projectId, PublisherId $publisherId, $name)
    {
        $this->projectId = $projectId;
        $this->publisherId = $publisherId;
        $this->name = $name;
    }

    public static function create(ProjectId $projectId, PublisherId $publisherId, $name)
    {
        return new self($projectId, $publisherId, $name);
    }
}