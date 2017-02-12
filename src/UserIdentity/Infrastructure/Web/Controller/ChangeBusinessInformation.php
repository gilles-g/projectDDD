<?php

namespace UserIdentity\Infrastructure\Web\Controller;

use Prooph\ServiceBus\Exception\CommandDispatchException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use UserIdentity\Domain\Command\ChangeBusinessInformations;

class ChangeBusinessInformation extends Controller
{
    public function formAction(Request $request, $id)
    {
        if ($request->isMethod('POST')) {
            /** @var ChangeBusinessInformations $command */
            $command = $this->get('prooph_service_bus.message_factory')->createMessageFromArray(ChangeBusinessInformations::class, [
                'payload' => [
                    'publisher_id' => $id,
                    'company_name' => 'lexik',
                    'vat_number' => '34222',
                    'siret' => sprintf("%'.013s'", rand(1, 300)),
                ],
            ]);

            try {
                $this->get('prooph_service_bus.user_command_bus')->dispatch($command);

                return new RedirectResponse($this->generateUrl('app_register_user_success'));
            } catch (CommandDispatchException $ex) {
                $params = $ex->getFailedDispatchEvent()->getParams();
                $this->addFlash('danger', 'command dispatch exception');
                $this->addFlash('danger', $ex->getMessage());
            } catch (\Throwable $error) {

                $this->addFlash('danger', 'throwable');
                $this->addFlash('danger', $error->getMessage());
            } catch (\Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render('@App/UserIdentity/change_business_informations.html.twig', [
           // 'form' => $form->createView(),
        ]);
    }
}