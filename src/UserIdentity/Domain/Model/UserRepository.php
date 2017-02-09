<?php

namespace UserIdentity\Domain\Model;

interface UserRepository
{
    public function add(User $user);

    public function remove(User $user);

    public function userWithUsername($username);

    public function all();

    public function userOfId(UserId $userId);

    /**
     * @return UserId
     */
    public function nextIdentity();
}