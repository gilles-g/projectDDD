<?php

namespace UserIdentity\Domain\Model;

interface PublisherRepository
{
    public function add(Publisher $publisher);

    public function remove(Publisher $publisher);

    public function publisherOfId(PublisherId $publisherId);

    /**
     * @return PublisherId
     */
    public function nextIdentity();
}