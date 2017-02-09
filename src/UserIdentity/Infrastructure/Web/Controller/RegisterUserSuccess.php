<?php

namespace UserIdentity\Infrastructure\Web\Controller;

use AppBundle\Model\LoggedUser;
use Prooph\EventStore\Stream\StreamName;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RegisterUserSuccess extends Controller
{
    public function successAction()
    {
        /** @var LoggedUser $tokenUser */
        $tokenUser = $this->getUser();

        $user = $this->get('user_identity.infrastructure.projection.user_finder')->byId($tokenUser->getUserId());
        $publisher = $this->get('publisher_repository')->getAggregateRoot($tokenUser->getPublisherId()->toString());

        $eventsUser = $this->get('prooph_event_store.doctrine_adapter.user_store')->loadEvents(new StreamName('event'), [
            'aggregate_id' => $tokenUser->getUserId()->toString(),
        ]);

        $eventsPublisher = $this->get('prooph_event_store.doctrine_adapter.user_store')->loadEvents(new StreamName('event'), [
            'aggregate_id' => $tokenUser->getPublisherId()->toString(),
        ]);

        return $this->render('@App/UserIdentity/register_user_success.html.twig', [
            'user' => $user,
            'publisher' => $publisher,
            'eventsUser' => $eventsUser,
            'eventsPublisher' => $eventsPublisher,
        ]);
    }
}