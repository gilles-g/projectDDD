<?php

namespace UserIdentity\Infrastructure\Web\Actions;

use AppBundle\Model\LoggedUser;
use Prooph\Common\Messaging\MessageFactory;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\Exception\CommandDispatchException;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use UserIdentity\Domain\Command\RegisterLightPublisher;
use UserIdentity\Domain\Model\PublisherId;
use UserIdentity\Domain\Model\UserId;
use UserIdentity\Infrastructure\Web\Form\RegisterPublisherType;

class RegisterUser
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var EngineInterface
     */
    private $twigEngine;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

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
        FormFactory $formFactory, CommandBus $commandBus, MessageFactory $messageFactory, EngineInterface $twigEngine,
        UrlGeneratorInterface $urlGenerator, TokenStorageInterface $tokenStorage, RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher)
    {
        $this->formFactory = $formFactory;
        $this->commandBus = $commandBus;
        $this->messageFactory = $messageFactory;
        $this->twigEngine = $twigEngine;
        $this->urlGenerator = $urlGenerator;
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(Request $request)
    {
        $form = $this->formFactory->create(RegisterPublisherType::class, [
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var RegisterLightPublisher $command */
            $command = $this->messageFactory->createMessageFromArray(RegisterLightPublisher::class, [
                'payload' => [
                    'email' => $form->get('email')->getData(),
                    'password' => $form->get('password')->getData(),
                    'publisher_id' => PublisherId::generate()->toString(),
                    'user_id' => UserId::generate()->toString(),
                    'roles' => 'ROLE_USER',
                ],
            ]);

            try {
                $this->commandBus->dispatch($command);

                $token = new UsernamePasswordToken(
                    new LoggedUser($command->userId(), $command->publisherId()), null, 'secured_area', [$command->roles()]
                );
                $this->tokenStorage->setToken($token);

                $event = new InteractiveLoginEvent($this->requestStack->getCurrentRequest(), $token);
                $this->eventDispatcher->dispatch('security.interactive_login', $event);

                $request->getSession()->getFlashBag()->add('success', 'Done.');

                return new RedirectResponse($this->urlGenerator->generate('app_register_user_success'));
            } catch (CommandDispatchException $ex) {
                $params = $ex->getFailedDispatchEvent()->getParams();
                $request->getSession()->getFlashBag()->add('danger', $ex->getMessage());
            } catch (\Throwable $error) {
                $request->getSession()->getFlashBag()->add('danger', $error->getMessage());
            } catch (\Exception $e) {
                $request->getSession()->getFlashBag()->add('danger', $e->getMessage());
            }
        }

        return $this->twigEngine->renderResponse('@App/UserIdentity/register_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}