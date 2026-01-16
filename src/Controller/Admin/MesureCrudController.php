<?php

namespace App\Controller\Admin;

use App\Entity\Mesure;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class MesureCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Mesure::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('aquarium', 'Aquarium cible'), 
            DateTimeField::new('dateSaisie', 'Date de mesure'),
            NumberField::new('temperature', 'Temp °C'),
            NumberField::new('ph', 'pH'),
            IntegerField::new('gh', 'GH'),
            IntegerField::new('kh', 'KH'),
            NumberField::new('nitrites', 'Nitrites (NO2)'),
            NumberField::new('ammonium', 'Ammonium (NH4)'),
        ];
    }
}