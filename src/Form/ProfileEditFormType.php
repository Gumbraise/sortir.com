<?php

namespace App\Form;

use App\Entity\Participant;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ProfileEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo',TextType::class,[
                'label' => 'Pseudonyme',
                'required' => true,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[A-Za-z0-9\s@$&_-]+$/',
                        'message' => 'Votre pseudo comporte des caractères non-autorisés.',
                    ]),
                ],
            ])
            ->add('prenom',TextType::class,[
                'label' => 'Prénom',
                'required' => true,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[A-Za-z\s\'-]+$/',
                        'message' => 'Votre prénom comporte des caractères non-autorisés.',
                    ]),
                ],
            ])
            ->add('nom',TextType::class,[
                'label' => 'Nom',
                'required' => true,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[A-Za-z\s\'-]+$/',
                        'message' => 'Votre nom comporte des caractères non-autorisés.',
                    ]),
                ],
            ])
            ->add('telephone',TextType::class,[
                'label' => 'Téléphone',
                'required' => true,
                'attr' => ['placeholder' => 'Ex : +33600000000'],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^\+(?:[0-9]?){6,14}[0-9]$/',
                        'message' => 'Votre téléphone doit être au format international sans espaces.',
                    ]),
                ],
            ])
            ->add('email',EmailType::class, [
                'label' => 'E-mail',
                'required' => true,
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
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
            ])
            ->add('campus')
            //->add('profilePictureName')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
