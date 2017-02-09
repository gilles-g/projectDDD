<?php

namespace UserIdentity\Infrastructure\Web\Actions;

use Prooph\Common\Messaging\MessageFactory;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\Exception\CommandDispatchException;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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

    public function __construct(
        FormFactory $formFactory, CommandBus $commandBus, MessageFactory $messageFactory, EngineInterface $twigEngine,
        UrlGeneratorInterface $urlGenerator)
    {
        $this->formFactory = $formFactory;
        $this->commandBus = $commandBus;
        $this->messageFactory = $messageFactory;
        $this->twigEngine = $twigEngine;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(Request $request)
    {
        $form = $this->formFactory->create(RegisterPublisherType::class, [
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $command = $this->messageFactory->createMessageFromArray(RegisterLightPublisher::class, [
                'payload' => [
                    'email' => $form->get('email')->getData(),
                    'password' => $form->get('password')->getData(),
                    'publisher_id' => PublisherId::generate()->toString(),
                    'user_id' => UserId::generate()->toString(),
                ]
            ]);

            try {
                $this->commandBus->dispatch($command);
                $request->getSession()->getFlashBag()->add('success', 'Done.');

                return new RedirectResponse($this->urlGenerator->generate('app_register_user_success'));
            } catch (CommandDispatchException $ex) {
                $params = $ex->getFailedDispatchEvent()->getParams();
                $request->getSession()->getFlashBag()->add('alert', $ex->getMessage());
            } catch (\Throwable $error) {
                $request->getSession()->getFlashBag()->add('alert', $error->getMessage());
            } catch (\Exception $e) {
                $request->getSession()->getFlashBag()->add('alert', $e->getMessage());
            }
        }

        return $this->twigEngine->renderResponse('@App/UserIdentity/register_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}