<?php

namespace App\Form;

use App\Entity\Lieu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom : *',
                'required' => true,
            ])
            ->add('rue', TextType::class,[
                'label' => 'Rue : *',
                'required' => true,
            ])
            ->add('latitude', NumberType::class,[
                'label' => 'Latitude : *',
                'required' => true,
            ])
            ->add('longitude', NumberType::class,[
                'label' => 'Longitude : *',
                'required' => true,
            ])
            ->add('ville', EntityType::class, [
                'label' => 'Ville : *',
                'required' => true,
                'class' => 'App\Entity\Ville',
                'choice_label' => 'nom',
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
