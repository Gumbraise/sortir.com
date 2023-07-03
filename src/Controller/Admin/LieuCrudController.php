<?php

namespace App\Controller\Admin;

use App\Entity\Lieu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class LieuCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Lieu::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nom', 'Nom du lieu'),
            TextField::new('rue', 'Rue'),
            NumberField::new('latitude', 'Latitude'),
            NumberField::new('longitude', 'Longitude'),
            AssociationField::new('ville', 'Ville'),
        ];
    }
}
