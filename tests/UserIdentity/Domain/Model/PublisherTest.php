<?php

namespace tests\UserIdentity\Domain\Model;

use UserIdentity\Domain\Model\Publisher;
use UserIdentity\Domain\Model\PublisherId;
use UserIdentity\Domain\Model\User;
use UserIdentity\Domain\Model\UserId;

class PublisherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test()
     */
    public function create()
    {
        $user = User::create(UserId::generate());
        $publisher = Publisher::create(PublisherId::generate(), $user->getUserId());

        $this->assertEquals($publisher->getUserId(), $user->getUserId());
    }
}