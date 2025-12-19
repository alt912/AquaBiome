<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IdPageController extends AbstractController
{
    #[Route('/idPage', name: 'idPage')]
    public function index(): Response
    {
        return $this->render('id_page/index.html.twig', [
            'controller_name' => 'IdPageController',
        ]);
    }
}
