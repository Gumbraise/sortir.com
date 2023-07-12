<?php

namespace App\Form;

use App\Entity\Participant;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProfileEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
//        $profilePictureFile = TextareaField::new('profilePictureFile', 'Photo de profil')
//            ->setFormType(VichImageType::class)
//            ->hideOnIndex();
//        $profilePictureName = ImageField::new('profilePictureName', 'Photo de profil')
//            ->setBasePath('/images/pictures');

        $builder
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudonyme : *',
                'required' => true,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[A-Za-z0-9\s@$&_-]+$/',
                        'message' => 'Votre pseudo comporte des caractères non-autorisés.',
                    ]),
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom : *',
                'required' => true,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[A-Za-z\s\'À-ÿ-]+$/u',
                        'message' => 'Votre prénom comporte des caractères non-autorisés.',
                    ]),
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom : *',
                'required' => true,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[A-Za-z\s\'À-ÿ-]+$/u',
                        'message' => 'Votre nom comporte des caractères non-autorisés.',
                    ]),
                ],
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone : *',
                'required' => true,
                'attr' => ['placeholder' => 'Ex : +33600000000'],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^\+\d{2}\s?(?:\(\d\))?\d(?:\s?\d{2}){4}$/',
                        'message' => 'Votre téléphone doit être au format international.',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail : *',
                'required' => true,
            ])
            ->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'invalid_message' => 'Les deux mots de passe doivent être les mêmes.',
                    'mapped' => false,
                    'required' => false,

                    'first_options' => [
                        'label' => 'Mot de passe :',
                        'attr' => ['autocomplete' => 'new-password'],
                        'constraints' => [
                            new Length([
                                'min' => 6,
                                'minMessage' => 'Your password should be at least {{ limit }} characters',
                                // max length allowed by Symfony for security reasons
                                'max' => 4096,
                            ]),
                        ],
                    ],
                    'second_options' => [
                        'label' => 'Confirmation du mot de passe :',
                        'attr' => ['autocomplete' => 'new-password'],
                        'constraints' => [
                            new Length([
                                'min' => 6,
                                'minMessage' => 'Your password should be at least {{ limit }} characters',
                                // max length allowed by Symfony for security reasons
                                'max' => 4096,
                            ]),
                        ],
                    ]
                ]
            )
            ->add('profilePictureFile', VichImageType::class, [
                'label' => 'Photo de profil : ',
                'required' => false,
                'allow_delete' => false,
                'download_uri' => false,
                'image_uri' => false,
                'asset_helper' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
