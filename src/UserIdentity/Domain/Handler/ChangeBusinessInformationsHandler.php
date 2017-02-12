<?php

namespace UserIdentity\Domain\Handler;

use UserIdentity\Domain\Command\ChangeBusinessInformations;
use UserIdentity\Domain\Exception\PublisherNotFound;
use UserIdentity\Domain\Model\Publisher;
use UserIdentity\Domain\Model\PublisherRepository;

class ChangeBusinessInformationsHandler
{
    /**
     * @var PublisherRepository
     */
    private $publisherRepository;

    public function __construct(PublisherRepository $publisherRepository)
    {
        $this->publisherRepository = $publisherRepository;
    }

    public function __invoke(ChangeBusinessInformations $command)
    {
        /** @var Publisher $publisher */
        $publisher = $this->publisherRepository->publisherOfId($command->publisherId());

        if (!$publisher) {
            throw PublisherNotFound::withUserId($command->publisherId());
        }

        $publisher->changeBusinessInformations($command->businessInformations());
    }
}