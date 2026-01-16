<?php

namespace App\Controller\Admin;

use App\Entity\Alerte;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class AlerteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Alerte::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // Sélection de l'aquarium (Corrige l'erreur aquarium_id cannot be null)
            AssociationField::new('aquarium', 'Aquarium concerné'),
            
            // On utilise 'nom' au lieu de 'titre'
            TextField::new('nom', 'Titre de l\'alerte'),
            
            // Unité (ex: °C, pH)
            TextField::new('unite', 'Unité de mesure'),
            
            // On utilise 'messageAlerte' au lieu de 'message'
            TextareaField::new('messageAlerte', 'Message détaillé'),
            
            // Date de l'alerte
            DateTimeField::new('dateAlerte', 'Date et Heure'),
        ];
    }
}