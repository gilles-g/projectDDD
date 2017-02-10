<?php

namespace UserIdentity\Infrastructure\Rx;

use AppBundle\Model\LoggedUser;
use Prooph\EventStore\Adapter\Doctrine\DoctrineEventStoreAdapter;
use Prooph\EventStore\Stream\StreamName;
use Prooph\ServiceBus\QueryBus;
use Rx\Observable;
use Rx\React\Promise;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use UserIdentity\Domain\Model\Publisher;
use UserIdentity\Domain\Model\User;
use UserIdentity\Domain\Query\GetPublisherById;
use UserIdentity\Domain\Query\GetUserById;

class RegisterSuccess
{
    /**
     * @var DoctrineEventStoreAdapter
     */
    private $doctrineEventStoreAdapter;

    /**
     * @var QueryBus
     */
    private $queryBus;

    /**
     * @var LoggedUser
     */
    private $tokenUser;

    public function __construct(
        DoctrineEventStoreAdapter $doctrineEventStoreAdapter,
        TokenStorageInterface $tokenStorage,
        QueryBus $queryBus
    )
    {
        $this->doctrineEventStoreAdapter = $doctrineEventStoreAdapter;
        $this->queryBus = $queryBus;
        $this->tokenUser = $tokenStorage->getToken()->getUser();
    }

    /**
     * @return Observable\AnonymousObservable
     */
    private function getUser()
    {
        $promiseUser = $this->queryBus->dispatch(new GetUserById($this->tokenUser->getUserId()->toString()));

        return Promise::toObservable($promiseUser)
            // return array
            ->map(function (User $user) {
                return [
                    'userId' => $user->getUserId(),
                    'emailAddress' => $user->getEmailAddress(),
                    'username' => $user->getUsername(),
                ];
            });
    }

    /**
     * @return Observable\AnonymousObservable
     */
    private function getUserEvents()
    {
        return Observable::fromIterator($this->doctrineEventStoreAdapter
            ->loadEvents(new StreamName('event'), [
                'aggregate_id' => $this->tokenUser->getUserId()->toString(),
            ]))
            ->reduce(function ($acc, $x) {
                return array_merge($acc, [$x]);
            }, []);
    }

    /**
     * @return Observable\AnonymousObservable
     */
    private function getPublisher()
    {
        $promisePublisher = $this->queryBus->dispatch(new GetPublisherById($this->tokenUser->getPublisherId()->toString()));

        return Promise::toObservable($promisePublisher)
            // return array
            ->map(function (Publisher $publisher) {
                return [
                    'publisherId' => $publisher->getPublisherId(),
                ];
            });
    }

    /**
     * @return Observable\AnonymousObservable
     */
    private function getPublisherEvents()
    {
        return Observable::fromIterator($this->doctrineEventStoreAdapter
            ->loadEvents(new StreamName('event'), [
                'aggregate_id' => $this->tokenUser->getPublisherId()->toString(),
            ]))
            ->reduce(function ($acc, $x) {
                return array_merge($acc, [$x]);
            }, []);
    }

    /**
     * @return Observable\AnonymousObservable
     */
    public function getSource()
    {
        $sourceUser = $this->getUser()->zip([$this->getUserEvents()], function ($userMap, $eventUserMap) {
            $userMap['userEvents'] = $eventUserMap;
            return $userMap;
        });

        $sourcePublisher = $this->getPublisher()
            ->zip([$this->getPublisherEvents()], function ($publisherMap, $eventPublisherMap) {
                $publisherMap['publisherEvents'] = $eventPublisherMap;
                return $publisherMap;
            });

        return $sourceUser->zip([$sourcePublisher], function ($user, $publisher) {
            return array_merge($user, $publisher);
        });
    }
}