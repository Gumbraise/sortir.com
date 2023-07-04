<?php

namespace App\Controller\Admin;

use App\Entity\Sortie;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SortieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sortie::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nom', 'Nom de la sortie'),
            DateTimeField::new('dateHeureDebut', 'Date et heure de la sortie'),
            DateTimeField::new('dateLimiteInscription', 'Date limite d\'inscription'),
            IntegerField::new('nbInscriptionMax', 'Nombre de places'),
            IntegerField::new('duree', 'Durée'),
            TextareaField::new('infosSortie', 'Informations sur la sortie'),
            AssociationField::new('etat', 'Etat de la sortie'),
            AssociationField::new('organisateur', 'Organisateur de la sortie'),
            AssociationField::new('participants', 'Participants à la sortie'),
            AssociationField::new('lieu', 'Lieu de la sortie'),
            AssociationField::new('campus', 'Campus de la sortie'),
        ];
    }
}
