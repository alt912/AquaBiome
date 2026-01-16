<?php

namespace App\Controller\Admin;

use App\Entity\Tache;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class TacheCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tache::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('aquarium', 'Aquarium'),
            TextField::new('nom', 'Nom de la tâche'),
            BooleanField::new('estFaite', 'Terminée ?'),
        ];
    }
}