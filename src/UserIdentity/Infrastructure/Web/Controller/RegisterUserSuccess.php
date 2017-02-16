<?php

namespace UserIdentity\Infrastructure\Web\Controller;

use Rx\Observer\CallbackObserver;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RegisterUserSuccess extends Controller
{
    public function successAction()
    {
        $registerSuccessRx = $this->get('user_identity.infrastructure.rx.register_success');

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