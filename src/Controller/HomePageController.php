<?php

namespace App\Controller;

use App\Repository\MesureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomePageController extends AbstractController
{
    #[Route('/', name: 'app_root')]
    #[Route('/homePage', name: 'homePage')]
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

        // 4. Gestion des ALERTES (Nouveau)
        $alertes = [];

        if ($derniereMesure) {
            // Vérification pH (Idéal : 6.5 - 7.5)
            $ph = $derniereMesure->getPh();
            if ($ph !== null && ($ph < 6.5 || $ph > 7.5)) {
                $alertes[] = [
                    'type' => 'warning', // classe Bootstrap 'alert-warning'
                    'message' => "Attention : Le pH est anormal ($ph). La valeur idéale est comprise entre 6.5 et 7.5."
                ];
            }

            // Vérification Température (Idéal : 24 - 28)
            $temp = $derniereMesure->getTemperature();
            if ($temp !== null && ($temp < 24 || $temp > 28)) {
                $alertes[] = [
                    'type' => 'danger', // classe Bootstrap 'alert-danger'
                    'message' => "Attention : Température critique ($temp °C). Elle doit être comprise entre 24°C et 28°C."
                ];
            }

            // Vérification Nitrites (doit être proche de 0, alerte si > 0.5)
            $nitrites = $derniereMesure->getNitrites();
            if ($nitrites !== null && $nitrites > 0.5) {
                $alertes[] = [
                    'type' => 'danger',
                    'message' => "Danger : Taux de nitrites élevé ($nitrites mg/L). Risque toxique pour les poissons !"
                ];
            }
        }

        // 5. On envoie les données à la vue
        return $this->render('home_page/index.html.twig', [
            'mesure' => $derniereMesure,
            'alertes' => $alertes, // On passe les alertes à la vue
            'chartData' => json_encode($historiqueData),
        ]);
    }
}