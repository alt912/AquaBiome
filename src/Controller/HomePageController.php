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
<<<<<<< Updated upstream
        // On force la recherche sur l'utilisateur 1 (celui utilisé pour l'ajout)
        $user = $userRepo->find(1);

        $derniereMesure = null;
        if ($user) {
            $derniereMesure = $mesureRepo->findOneBy(
                ['utilisateur' => $user],
                ['dateSaisie' => 'DESC']
            );
=======
        // 1. On récupère la toute dernière mesure enregistrée en base (peu importe qui l'a faite)
        $derniereMesure = $mesureRepo->findOneBy([], ['dateSaisie' => 'DESC']);

        // 2. On récupère TOUTES les mesures pour les graphiques D3.js
        $toutesLesMesures = $mesureRepo->findBy([], ['dateSaisie' => 'ASC']);

        $historiqueData = [];
        
        // 3. On prépare les données pour JavaScript
        foreach ($toutesLesMesures as $m) {
            $historiqueData[] = [
                'date' => $m->getDateSaisie() ? $m->getDateSaisie()->format('Y-m-d H:i') : null,
                'gh' => $m->getGh(),
                'ph' => $m->getPh(),
                'kh' => $m->getKh(),
                'nitrites' => $m->getNitrites(),
                'ammonium' => $m->getAmmonium(),
            ];
>>>>>>> Stashed changes
        }

        return $this->render('home_page/index.html.twig', [
            'mesure' => $derniereMesure,
<<<<<<< Updated upstream
=======
            // On envoie les données formatées en JSON pour D3.js
            'chartData' => json_encode($historiqueData),
>>>>>>> Stashed changes
        ]);
    }
}