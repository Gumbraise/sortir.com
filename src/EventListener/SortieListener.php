<?php

namespace App\EventListener;

use App\Entity\Sortie;
use App\Utils\SendMail;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Sortie::class)]
class SortieListener
{
    public function __construct(
        private SendMail $sendMail,
    )
    {
    }

    public function postPersist(Sortie $sortie, PostPersistEventArgs $event): void
    {
        $this->sendMail->TemplatedEmail(
            $sortie->getOrganisateur()->getEmail(),
            'no-reply@sortir.com',
            'Votre sortie ' . $sortie . ' a bien été créée !',
            '_mailer/sortie/created.html.twig',
            ['sortie' => $sortie]
        );
    }
}