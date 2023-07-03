<?php

namespace App\Controller\Admin;

use App\Entity\Participant;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ParticipantCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Participant::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $profilePictureFile = TextareaField::new('profilePictureFile', 'Photo de profil')
            ->setFormType(VichImageType::class)
            ->hideOnIndex();
        $profilePictureName = ImageField::new('profilePictureName', 'Photo de profil')
            ->setBasePath('/images/pictures');

        $fields = [
            TextField::new('prenom', 'Prénom'),
            TextField::new('nom', 'Nom'),
            TelephoneField::new('telephone', 'Numéro de téléphone'),
            EmailField::new('email', 'Adresse mail'),
            ArrayField::new('roles', 'Rôles'),
            AssociationField::new('campus', 'Campus')
            ->setRequired(false),
        ];

        if ($pageName == Crud::PAGE_INDEX || $pageName == Crud::PAGE_DETAIL) {
            $fields[] = $profilePictureName;
        } else {
            $fields[] = $profilePictureFile;
        }

        return $fields;

    }
}
