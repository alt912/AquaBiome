<?php

namespace App\Controller;

use App\Repository\MesureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomePageController extends AbstractController
{
    #[Route('/', name: 'homePage')]
    public function index(MesureRepository $mesureRepo): Response
    {
        // 1. On récupère la toute dernière mesure pour l'affichage des chiffres clés
        $derniereMesure = $mesureRepo->findOneBy([], ['dateSaisie' => 'DESC']);

        // 2. On récupère TOUTES les mesures triées par date pour les graphiques D3.js
        $toutesLesMesures = $mesureRepo->findBy([], ['dateSaisie' => 'ASC']);

        $historiqueData = [];
        
        // 3. On prépare les données pour le JavaScript
        foreach ($toutesLesMesures as $m) {
            $historiqueData[] = [
                'date' => $m->getDateSaisie() ? $m->getDateSaisie()->format('Y-m-d H:i') : null,
                'gh' => $m->getGh(),
                'ph' => $m->getPh(),
                'kh' => $m->getKh(),
                'nitrites' => $m->getNitrites(),
                'ammonium' => $m->getAmmonium(),
            ];
        }

        // 4. On envoie les données à la vue
        return $this->render('home_page/index.html.twig', [
            'mesure' => $derniereMesure,
            'chartData' => json_encode($historiqueData),
        ]);
    }
}