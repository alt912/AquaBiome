<?php

namespace App\Controller;

use App\Repository\AlerteRepository;
use App\Repository\MesureRepository;
use App\Repository\TacheRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

final class HomePageController extends AbstractController
{
    #[Route('/', name: 'app_root')]
    #[Route('/homePage', name: 'homePage')]
    public function index(MesureRepository $mesureRepo, AlerteRepository $alerteRepo, TacheRepository $tacheRepo, ChartBuilderInterface $chartBuilder): Response
    {
        // 1. On récupère la toute dernière mesure pour l'affichage des chiffres clés
        $derniereMesure = $mesureRepo->findOneBy([], ['dateSaisie' => 'DESC']);

        // 2. On récupère TOUTES les mesures triées par date
        $toutesLesMesures = $mesureRepo->findBy([], ['dateSaisie' => 'ASC']);

        // Préparation des données pour UX Chart.js
        $labels = [];
        $dataGh = [];
        $dataPh = [];
        $dataKh = [];
        $dataNitrites = [];
        $dataAmmonium = [];

        foreach ($toutesLesMesures as $m) {
            $labels[] = $m->getDateSaisie() ? $m->getDateSaisie()->format('d/m H:i') : '';
            $dataGh[] = $m->getGh();
            $dataPh[] = $m->getPh();
            $dataKh[] = $m->getKh();
            $dataNitrites[] = $m->getNitrites();
            $dataAmmonium[] = $m->getAmmonium();
        }

        // --- GRAPHIQUE GH ---
        $chartGh = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chartGh->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Dureté (GH)',
                    'backgroundColor' => 'rgba(9, 132, 227, 0.2)',
                    'borderColor' => 'rgb(9, 132, 227)',
                    'data' => $dataGh,
                    'tension' => 0.4,
                ],
            ],
        ]);

        // --- GRAPHIQUE PH / KH ---
        $chartPhKh = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chartPhKh->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'pH',
                    'borderColor' => 'rgb(255, 118, 117)',
                    'data' => $dataPh,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'KH',
                    'borderColor' => 'rgb(0, 184, 148)',
                    'data' => $dataKh,
                    'yAxisID' => 'y1',
                ],
            ],
        ]);

        // --- GRAPHIQUE TOXINES ---
        $chartToxic = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chartToxic->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Nitrites (NO2)',
                    'backgroundColor' => 'rgb(214, 48, 49)',
                    'data' => $dataNitrites,
                ],
                [
                    'label' => 'Ammonium (NH4)',
                    'backgroundColor' => 'rgb(225, 112, 85)',
                    'data' => $dataAmmonium,
                ],
            ],
        ]);

        // 4. Gestion des ALERTES
        $latestMesureId = $derniereMesure ? $derniereMesure->getId() : null;
        $alertes = $alerteRepo->findRelevantAlerts($latestMesureId);

        // 5. Gestion des TÂCHES
        $taches = $tacheRepo->createQueryBuilder('t')
            ->where('t.status != :done')
            ->andWhere('t.deadline <= :now')
            ->setParameter('done', 'Terminée')
            ->setParameter('now', new \DateTime())
            ->orderBy('t.priorite', 'ASC')
            ->addOrderBy('t.deadline', 'ASC')
            ->getQuery()
            ->getResult();

        // 6. Conversion des TÂCHES EN RETARD
        $now = new \DateTime();
        foreach ($taches as $tache) {
            if ($tache->getDeadline() < $now->setTime(0, 0, 0)) {
                $alerteRetard = new \App\Entity\Alerte();
                $alerteRetard->setNom("RETARD TÂCHE");
                $alerteRetard->setMessageAlerte("La tâche '" . $tache->getTitre() . "' est en retard ! (prévue le " . $tache->getDeadline()->format('d/m/Y') . ")");
                $alerteRetard->setDateAlerte(new \DateTime());
                $alertes[] = $alerteRetard;
            }
        }

        return $this->render('home_page/index.html.twig', [
            'mesure' => $derniereMesure,
            'alertes' => $alertes,
            'taches' => $taches,
            'chartGh' => $chartGh,
            'chartPhKh' => $chartPhKh,
            'chartToxic' => $chartToxic,
        ]);
    }

    #[Route('/tache/{id}/complete', name: 'app_tache_complete')]
    public function complete(\App\Entity\Tache $tache, \Doctrine\ORM\EntityManagerInterface $em): Response
    {
        // Si la tâche est récurrente
        if ($tache->getRecurrenceJours() > 0) {
            // On repousse la date de délai
            $jours = $tache->getRecurrenceJours();
            // On repart de "Maintenant" + X jours
            $nouvelleDate = new \DateTime();
            $nouvelleDate->modify("+$jours days");

            $tache->setDeadline($nouvelleDate);
            $tache->setStatus('À faire'); // Au cas où

            $this->addFlash('info', "Tâche validée ! Elle reviendra dans $jours jours.");
        } else {
            // Sinon, on la marque comme terminée (elle disparaîtra de la liste "À faire")
            $tache->setStatus('Terminée');
            $tache->setDateCompletion(new \DateTime());

            $this->addFlash('success', "Tâche terminée !");
        }

        $em->flush();

        return $this->redirectToRoute('homePage');
    }

    #[Route('/alerte/{id}/dismiss', name: 'app_alerte_dismiss', methods: ['POST'])]
    public function dismissAlerte(int $id, AlerteRepository $alerteRepo): JsonResponse
    {
        $alerteRepo->dismissAlert($id);
        return new JsonResponse(['ok' => true]);
    }
}