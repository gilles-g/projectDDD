<?php

namespace UserIdentity\Infrastructure\Web\Controller;

use Rx\Observable;
use Rx\Observer\CallbackObserver;
use Rx\React\Promise;
use Symfony\Component\HttpFoundation\Response;
use UserIdentity\Domain\Model\LoggedUserInterface;
use Prooph\EventStore\Stream\StreamName;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UserIdentity\Domain\Model\Publisher;
use UserIdentity\Domain\Model\User;
use UserIdentity\Domain\Query\GetPublisherById;
use UserIdentity\Domain\Query\GetUserById;

class RegisterUserSuccess extends Controller
{
    public function successAction()
    {
        /**
         * Example of RxPHP, need move all this code.
         *
         * Get user for a given userId
         *  - loads events for user and merge with user data
         * Get publisher for a given publisherId
         *  - loads events for publisher and merge with publisher data
         * Merge user data and publisher data
         *
         * return Observable Map
         */

        $queryBus = $this->get('prooph_service_bus.user_query_bus');
        /** @var LoggedUserInterface $tokenUser */
        $tokenUser = $this->getUser();

        $promiseUser = $queryBus->dispatch(new GetUserById($tokenUser->getUserId()->toString()));
        $promisePublisher = $queryBus->dispatch(new GetPublisherById($tokenUser->getPublisherId()->toString()));

        // Observable of User
        $userObs = Promise::toObservable($promiseUser)
            // return array
            ->map(function (User $user) {
                return [
                    'userId' => $user->getUserId(),
                    'emailAddress' => $user->getEmailAddress(),
                    'username' => $user->getUsername(),
                ];
            });

        // Iterator Observable of user events
        $eventUserObs = Observable::fromIterator(($this->get('prooph_event_store.doctrine_adapter.user_store')
            ->loadEvents(new StreamName('event'), [
                'aggregate_id' => $tokenUser->getUserId()->toString(),
            ])));

        // Combine user observable with latest user events observable
        $sourceUser = $userObs->combineLatest([$eventUserObs], function ($userMap, $eventUserMap) {
            $userMap['userEvents'][] = $eventUserMap;

            return $userMap;
        });

        // Observable of publisher
        $publisherObs = Promise::toObservable($promisePublisher)
            // return array
            ->map(function (Publisher $publisher) {
                return [
                    'publisherId' => $publisher->getPublisherId(),
                ];
            });

        // Iterator Observable of publisher events
        $eventsPublisher = Observable::fromIterator($this->get('prooph_event_store.doctrine_adapter.user_store')
            ->loadEvents(new StreamName('event'), [
                'aggregate_id' => $tokenUser->getPublisherId()->toString(),
            ]));

        // Combine publisher observable with latest publisher events observable
        $sourcePublisher =
            $publisherObs->combineLatest([$eventsPublisher], function ($publisherMap, $eventPublisherMap) {
                $publisherMap['publisherEvents'][] = $eventPublisherMap;

                return $publisherMap;
            });

        // Combine user observable and publisher observable
        $source = $sourceUser->combineLatest([$sourcePublisher], function ($user, $publisher) {
            return array_merge($user, $publisher);
        });

        // Observer
        $createResponse = function (Response $response) {
            return new CallbackObserver(
            // On success, render view
                function ($value) use ($response) {
                    $this->render('@App/UserIdentity/register_user_success.html.twig', [
                        'publisherUser' => $value,
                    ], $response);
                },

                // On error, set error
                function ($error) use ($response) {
                    $response->setStatusCode(404);
                    $response->setContent('error on query with message : ' . $error);
                }
            );
        };

        $response = new Response();
        $source->subscribe($createResponse($response));

        if ($response->getStatusCode() == '404') {
            throw $this->createNotFoundException($response->getContent());
        }

        return $response;
    }
}