<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AjoutDonneeController extends AbstractController
{
    #[Route('/ajoutDonnee', name: 'ajoutDonnee')]
    public function index(): Response
    {
        return $this->render('ajout_donnee/index.html.twig', [
            'controller_name' => 'AjoutDonneeController',
        ]);
    }
}
