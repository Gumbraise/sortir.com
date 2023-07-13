<?php
// src/EventSubscriber/TokenSubscriber.php
namespace App\EventSubscriber;

use App\Controller\SortieController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class IsConnectedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();

        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ($controller instanceof SortieController) {
            $user = $this->security->getUser();
            $session = $event->getRequest()->getSession();

            if ($user && !$user->getCampus() && !$this->security->isGranted('ROLE_ADMIN')) {
                $session->getFlashBag()->add('error', 'Vous n\'êtes associé à aucun campus');
                throw new AccessDeniedHttpException('Vous n\'êtes associé à aucun campus');
            } elseif(!$user) {
                $session->getFlashBag()->add('error', 'Vous devez être connecté pour accéder à cette page');
                throw new AccessDeniedHttpException('Vous devez être connecté pour accéder à cette page');
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}