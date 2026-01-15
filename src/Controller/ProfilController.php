<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'gestion_compte')]

    //#[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        // Récupération de l'utilisateur connecté
        $user = $this->getUser();

        return $this->render('profil/index.html.twig', [
            'user' => $user, 
        ]);
    }
}