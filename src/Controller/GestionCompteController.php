<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GestionCompteController extends AbstractController
{
    #[Route('/gestionCompte', name: 'gestionCompte')]
    public function index(): Response
    {
        return $this->render('gestion_compte/index.html.twig', [
            'controller_name' => 'GestionCompteController',
        ]);
    }
}
