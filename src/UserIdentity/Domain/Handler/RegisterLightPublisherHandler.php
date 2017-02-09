<?php

namespace UserIdentity\Domain\Handler;

use AppBundle\Model\LoggedUser;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
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

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        UserRepository $userRepository,
        PublisherRepository $publisherRepository,
        ChecksUniqueUsersEmailAddress $checksUniqueUsersEmailAddress,
        PasswordEncoderInterface $passwordEncoder,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->userRepository = $userRepository;
        $this->publisherRepository = $publisherRepository;
        $this->checksUniqueUsersEmailAddress = $checksUniqueUsersEmailAddress;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(RegisterLightPublisher $command)
    {
        $userId = call_user_func($this->checksUniqueUsersEmailAddress, $command->emailAddress());

        if ($userId) {
            throw UserAlreadyExists::withUserId($userId);
        }

        $passwordEncoded = $this->passwordEncoder->encodePassword($command->password(), null);

        $user = User::registerWithData($command->userId(), $command->emailAddress(), $passwordEncoded);
        $this->userRepository->add($user);

        $publisher = Publisher::registerLightPublisher($command->publisherId(), $user->getUserId());
        $this->publisherRepository->add($publisher);

        $token = new UsernamePasswordToken(new LoggedUser($user->getUserId(), $command->publisherId()), null, 'secured_area', $user->getRoles());
        $this->tokenStorage->setToken($token);

        $event = new InteractiveLoginEvent($this->requestStack->getCurrentRequest(), $token);
        $this->eventDispatcher->dispatch('security.interactive_login', $event);
    }
}