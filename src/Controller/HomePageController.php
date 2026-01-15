<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomePageController extends AbstractController
{
    #[Route('/', name: 'homePage')]
    public function index(): Response
    {
<<<<<<< Updated upstream
        return $this->render('home_page/index.html.twig', [
            'controller_name' => 'HomePageController',
=======
        $user = $userRepo->find(1);
        $derniereMesure = null;
        $historiqueData = [];

        if ($user) {
            // 1. On garde ta logique pour la boîte "Dernière mesure"
            $derniereMesure = $mesureRepo->findOneBy(
                ['utilisateur' => $user],
                ['dateSaisie' => 'DESC']
            );

            // 2. On récupère TOUTES les mesures pour les graphiques (triées par date)
            $toutesLesMesures = $mesureRepo->findBy(
                ['utilisateur' => $user],
                ['dateSaisie' => 'ASC']
            );

            // 3. On prépare le tableau pour JavaScript
            foreach ($toutesLesMesures as $m) {
                $historiqueData[] = [
                    'date' => $m->getDateSaisie()->format('Y-m-d H:i'),
                    'gh' => $m->getGh(),
                    'ph' => $m->getPh(),
                    'kh' => $m->getKh(),
                    'nitrites' => $m->getNitrites(),
                    'ammonium' => $m->getAmmonium(),
                ];
            }
        }

        return $this->render('home_page/index.html.twig', [
            'mesure' => $derniereMesure,
            // On transforme le tableau PHP en texte JSON pour le JS
            'chartData' => json_encode($historiqueData),
>>>>>>> Stashed changes
        ]);
    }
}
