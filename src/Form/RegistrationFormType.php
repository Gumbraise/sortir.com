<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'attr' => [
                    'placeholder' => "Votre pseudonyme"
                ],
                'required' => true,
            ])
            ->add('nom', TextType::class, [
                'attr' => [
                    'placeholder' => "Votre nom"
                ],
                'required' => true,
            ])
            ->add('prenom', TextType::class, [
                'attr' => [
                    'placeholder' => "Votre prénom"
                ],
                'required' => true,
            ])
            ->add('telephone', TelType::class, [
                'attr' => [
                    'placeholder' => "Votre numéro de téléphone"
                ],
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => "Votre adresse mail"
                ],
                'required' => true,
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'J\'accepte les termes et conditions',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les termes et conditions.',
                    ]),
                ],
                'required' => true,
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        'autocomplete' => 'password',
                    ],
                ],
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Entrez un mot de passe valide',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Votre mot de passe doit au moins avoir {{ limit }} caractères.',
                            'max' => 4096,
                        ]),
                    ],
                    'label' => 'Mot de passe',
                    'attr' => [
                        'placeholder' => "Votre mot de passe",
                    ]
                ],
                'second_options' => [
                    'label' => 'Répétez le mot de passe',
                    'attr' => [
                        'placeholder' => "Répétez votre mot de passe",
                    ]
                ],
                'invalid_message' => 'Les mots de passes doivent être les mêmes.',
                'mapped' => false,
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
