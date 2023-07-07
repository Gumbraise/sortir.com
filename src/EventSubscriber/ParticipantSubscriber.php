<?php

namespace App\EventSubscriber;

use App\Entity\Participant;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ParticipantSubscriber implements EventSubscriber
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        // Only hash the password for Participant entities
        if ($entity instanceof Participant) {
            $this->hashPassword($entity);
        }
    }
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        // Only hash the password for Participant entities
        if ($entity instanceof Participant) {
            $this->hashPassword($entity);
        }
    }

    public function hashPassword(Participant $user): void
    {
        $plainPassword = $user->getPassword();

        // If the password hasn't changed, no need to hash it again
        if (empty($plainPassword)) {
            return;
        }
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            )
        );
    }
}