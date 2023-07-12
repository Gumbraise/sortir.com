<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', SearchType::class, [
                'label' => 'Le nom de la sortie contient :',
                'required' => false,
            ])
            ->add('dateStart', DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'mapped' => false,
                'required' => false,
            ])
            ->add('dateEnd', DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'mapped' => false,
                'required' => false,
            ])
            ->add('isOrganisateur', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur/trice',
                'mapped' => false,
                'required' => false,
            ])
            ->add('isInscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit/e',
                'mapped' => false,
                'data' => true,
                'required' => false,
            ])
            ->add('isNotInscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit/e',
                'mapped' => false,
                'data' => true,
                'required' => false,
            ])
            ->add('isPast', CheckboxType::class, [
                'label' => 'Sorties passées',
                'mapped' => false,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
