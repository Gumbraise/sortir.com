<?php
namespace App\EventSubscriber;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class IsActifSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $user = $this->security->getUser();
        $session = $event->getRequest()->getSession();

        if ($user && !$user->isActif()) {
            $session->getFlashBag()->add('error', 'Erreur : Votre compte à été désactivé, veuillez contacter un responsable.');
            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_logout')));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}