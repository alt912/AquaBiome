<?php

namespace App\Controller;

use App\Repository\MesureRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomePageController extends AbstractController
{
    #[Route('/', name: 'homePage')]
    public function index(MesureRepository $mesureRepo, UserRepository $userRepo): Response
    {
        // On force la recherche sur l'utilisateur 1 (celui utilisé pour l'ajout)
        $user = $userRepo->find(1);

        $derniereMesure = null;
        if ($user) {
            $derniereMesure = $mesureRepo->findOneBy(
                ['utilisateur' => $user],
                ['dateSaisie' => 'DESC']
            );
        }

        return $this->render('home_page/index.html.twig', [
            'mesure' => $derniereMesure,
        ]);
    }
}