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
            TextField::new('titre', 'Titre de la tâche'),
            \EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField::new('deadline', 'Date limite / Echéance'),
            \EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField::new('recurrenceJours', 'Récurrence (jours)')->setHelp('Laisser vide si pas de récurrence. Mettre X jours pour qu\'elle revienne.'),
            \EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField::new('priorite', 'Priorité')->setChoices([
                'Basse' => 'Basse',
                'Moyenne' => 'Moyenne',
                'Haute' => 'Haute',
            ]),
            \EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField::new('typeAction', 'Type d\'action')->setChoices([
                'Nourriture' => 'Nourriture',
                'Entretien' => 'Entretien',
                'Changement eau' => 'Changement eau',
                'Autre' => 'Autre',
            ]),
            TextField::new('status', 'Statut')->hideOnForm(),
            AssociationField::new('utilisateur', 'Utilisateur assigné'),
        ];
    }
}