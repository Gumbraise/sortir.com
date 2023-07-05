<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use DateTimeImmutable;
use Symfony\Component\Form\AbstractType;
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
            ->add('dateHeureDebut', DateTimeType::class, [
                'input' => 'datetime_immutable',
                'label' => 'Entre',
                'required' => false,
                'widget' => 'single_text',
                'input_format' => 'd/m/Y',
                'empty_data' => (new DateTimeImmutable())->format(DateTimeType::HTML5_FORMAT),
            ])
            ->add('dateLimiteInscription', DateTimeType::class, [
                'input' => 'datetime_immutable',
                'label' => 'et',
                'required' => false,
                'widget' => 'single_text',
                'input_format' => 'd/m/Y',
                'empty_data' => (new DateTimeImmutable('9999/12/31'))->format(DateTimeType::HTML5_FORMAT),
            ])
            ->add('campus');
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
