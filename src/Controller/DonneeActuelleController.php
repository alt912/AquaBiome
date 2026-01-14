<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DonneeActuelleController extends AbstractController
{
    #[Route('/donnee/actuelle', name: 'donnees_actuelles')]
    public function index(): Response
    {
        return $this->render('donnee_actuelle/index.html.twig', [
            'controller_name' => 'DonneeActuelleController',
        ]);
    }
}
