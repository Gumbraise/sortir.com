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
                ]
            ])
            ->add('nom', TextType::class, [
                'attr' => [
                    'placeholder' => "Votre nom"
                ]
            ])
            ->add('prenom', TextType::class, [
                'attr' => [
                    'placeholder' => "Votre prénom"
                ]
            ])
            ->add('telephone', TelType::class, [
                'attr' => [
                    'placeholder' => "Votre numéro de téléphone"
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => "Votre adresse mail"
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'J\'accepte les termes et conditions',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
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
                'invalid_message' => 'The password fields must match.',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
