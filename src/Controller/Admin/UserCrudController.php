<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    private $passwordHasher;

    public function __construct(\Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('email'),
            TextField::new('nom'),
            \EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField::new('roles')
                ->setChoices([
                    'Utilisateur' => 'ROLE_USER',
                    'Professeur' => 'ROLE_PROF',
                    'Administrateur' => 'ROLE_ADMIN',
                ])
                ->allowMultipleChoices()
                ->renderExpanded(),
            TextField::new('password')
                ->setFormType(\Symfony\Component\Form\Extension\Core\Type\PasswordType::class)
                ->setRequired($pageName === \EasyCorp\Bundle\EasyAdminBundle\Config\Crud::PAGE_NEW)
                ->onlyOnForms(),
        ];
    }

    public function persistEntity(\Doctrine\ORM\EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->hashPassword($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(\Doctrine\ORM\EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->hashPassword($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    private function hashPassword($user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if ($user->getPassword()) {
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, $user->getPassword())
            );
        }
    }
}
