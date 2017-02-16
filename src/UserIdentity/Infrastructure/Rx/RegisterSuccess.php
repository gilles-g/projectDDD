<?php

namespace UserIdentity\Infrastructure\Rx;

use AppBundle\Model\LoggedUser;
use Prooph\EventStore\Adapter\Doctrine\DoctrineEventStoreAdapter;
use Prooph\EventStore\Stream\StreamName;
use Rx\Observable;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use UserIdentity\Domain\Model\Publisher;
use UserIdentity\Domain\Model\User;
use UserIdentity\Domain\Query\GetPublisherById;
use UserIdentity\Domain\Query\GetUserById;

/**
 * Class RegisterSuccess
 * @package UserIdentity\Infrastructure\Rx
 * @author Gauthier Gilles <g.gauthier@lexik.fr>
 */
class RegisterSuccess
{
    /**
     * @var DoctrineEventStoreAdapter
     */
    private $doctrineEventStoreAdapter;

    /**
     * @var QueryObservable
     */
    private $queryObservable;

    /**
     * @var LoggedUser
     */
    private $tokenUser;

    public function __construct(
        DoctrineEventStoreAdapter $doctrineEventStoreAdapter,
        TokenStorageInterface $tokenStorage,
        QueryObservable $queryObservable
    )
    {
        $this->doctrineEventStoreAdapter = $doctrineEventStoreAdapter;
        $this->queryObservable = $queryObservable;
        $this->tokenUser = $tokenStorage->getToken()->getUser();
    }

    /**
     * @return Observable\AnonymousObservable
     */
    private function getUser()
    {
        $userObs = $this->queryObservable->dispatch(new GetUserById($this->tokenUser->getUserId()->toString()));

        return $userObs
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
        $publisherObs = $this->queryObservable->dispatch(new GetPublisherById($this->tokenUser->getPublisherId()->toString()));

        return $publisherObs
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
     * Example of RxPHP
     *
     * Get user for a given userId
     *  - loads events for user and merge with user data
     * Get publisher for a given publisherId
     *  - loads events for publisher and merge with publisher data
     * Merge user data and publisher data
     *
     * return Observable Map
     *
     * @return Observable\AnonymousObservable
     */
    public function getSource()
    {
        $sourceUser = $this->getUser()
            ->zip([$this->getUserEvents()], function ($userMap, $eventUserMap) {
                $userMap['userEvents'] = $eventUserMap;
                return $userMap;
            })
        ;

        $sourcePublisher = $this->getPublisher()
            ->zip([$this->getPublisherEvents()], function ($publisherMap, $eventPublisherMap) {
                $publisherMap['publisherEvents'] = $eventPublisherMap;
                return $publisherMap;
            });

        return
            $sourceUser
                ->zip([$sourcePublisher], function ($user, $publisher) {
                    return array_merge($user, $publisher);
                });
    }
}