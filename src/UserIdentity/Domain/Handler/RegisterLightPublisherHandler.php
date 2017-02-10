<?php

namespace UserIdentity\Domain\Handler;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use UserIdentity\Domain\Command\RegisterLightPublisher;
use UserIdentity\Domain\Exception\UserAlreadyExists;
use UserIdentity\Domain\Model\Publisher;
use UserIdentity\Domain\Model\PublisherRepository;
use UserIdentity\Domain\Model\User;
use UserIdentity\Domain\Model\UserRepository;
use UserIdentity\Domain\Service\ChecksUniqueUsersEmailAddress;

class RegisterLightPublisherHandler
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var PublisherRepository
     */
    private $publisherRepository;

    /**
     * @var ChecksUniqueUsersEmailAddress
     */
    private $checksUniqueUsersEmailAddress;

    /**
     * @var PasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(
        UserRepository $userRepository,
        PublisherRepository $publisherRepository,
        ChecksUniqueUsersEmailAddress $checksUniqueUsersEmailAddress,
        PasswordEncoderInterface $passwordEncoder
    )
    {
        $this->userRepository = $userRepository;
        $this->publisherRepository = $publisherRepository;
        $this->checksUniqueUsersEmailAddress = $checksUniqueUsersEmailAddress;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function __invoke(RegisterLightPublisher $command)
    {
        $userId = call_user_func($this->checksUniqueUsersEmailAddress, $command->emailAddress());

        if ($userId) {
            throw UserAlreadyExists::withUserId($userId);
        }

        $passwordEncoded = $this->passwordEncoder->encodePassword($command->password(), null);

        $user = User::registerWithData($command->userId(), $command->emailAddress(), $passwordEncoded, $command->roles());
        $this->userRepository->add($user);

        $publisher = Publisher::registerLightPublisher($command->publisherId(), $user->getUserId());
        $this->publisherRepository->add($publisher);
    }
}