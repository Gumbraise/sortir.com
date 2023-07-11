<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\EventSubscriber\SortieAdminCampusSubscriber;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\User\UserInterface;

class SortieType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentUser = $this->security->getUser();
        $builder
            ->add('nom')
            ->add('dateHeureDebut')
            ->add('duree')
            ->add('dateLimiteInscription')
            ->add('nbInscriptionMax')
            ->add('infosSortie')
            //->add('etat')
            ->add('organisateur', EntityType::class, [
                'class' => 'App\Entity\Participant',
                'choice_label' => 'nom',
                'data' => $this->getParticipant($currentUser),
                'disabled' => true,
            ])
            //->add('participants')
            ->add('lieu')
        ;
        $roles = $currentUser->getRoles();
        if (in_array('ROLE_ADMIN', $roles, true)) {
            $builder->add('campus', EntityType::class, [
                'class' => 'App\Entity\Campus',
                'choice_label' => 'nom',
            ]);
        }
    }

    private function getParticipant(?UserInterface $user): ?Participant
    {
        if (!$user instanceof Participant) {
            return null;
        }

        return $user;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
