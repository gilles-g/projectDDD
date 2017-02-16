<?php

namespace UserIdentity\Infrastructure\Web\Controller;

use Rx\Observer\CallbackObserver;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UserIdentity\Domain\Model\PublisherId;
use UserIdentity\Infrastructure\Persistence\EventStorePublisherRepository;

class RegisterUserSuccess extends Controller
{
    public function successAction()
    {
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
         */

        $registerSuccessRx = $this->get('user_identity.infrastructure.rx.register_success');

        /** @var EventStorePublisherRepository $repo */
        $repo = $this->get('publisher_repository');


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
        $registerSuccessRx
            ->getSource()
            ->subscribe($createResponse($response));

        if ($response->getStatusCode() == '404') {
            throw $this->createNotFoundException($response->getContent());
        }

        return $response;
    }
}