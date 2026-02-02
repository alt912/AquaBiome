<?php

namespace App\Controller;

use App\Repository\AlerteRepository;
use App\Repository\MesureRepository;
use App\Repository\TacheRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomePageController extends AbstractController
{
    #[Route('/', name: 'app_root')]
    #[Route('/homePage', name: 'homePage')]
    public function index(MesureRepository $mesureRepo, AlerteRepository $alerteRepo, TacheRepository $tacheRepo): Response
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

        // 4. Gestion des ALERTES (Récupération intelligente)
        $latestMesureId = $derniereMesure ? $derniereMesure->getId() : null;
        $alertes = $alerteRepo->findRelevantAlerts($latestMesureId);

        // 5. Gestion des TÂCHES (Récurrentes ou non)
        // On récupère les tâches qui sont "À faire" ET dont la date est dépassée ou aujourd'hui
        $taches = $tacheRepo->createQueryBuilder('t')
            ->where('t.status != :done')
            ->andWhere('t.deadline <= :now')
            ->setParameter('done', 'Terminée')
            ->setParameter('now', new \DateTime())
            ->orderBy('t.priorite', 'ASC')
            ->addOrderBy('t.deadline', 'ASC')
            ->getQuery()
            ->getResult();

        // 6. Conversion des TÂCHES EN RETARD en ALERTES
        $now = new \DateTime();
        foreach ($taches as $tache) {
            // Si la date limite est passée (hier ou avant)
            if ($tache->getDeadline() < $now->setTime(0, 0, 0)) {
                $alerteRetard = new \App\Entity\Alerte();
                $alerteRetard->setNom("RETARD TÂCHE");
                $alerteRetard->setMessageAlerte("La tâche '" . $tache->getTitre() . "' est en retard ! (prévue le " . $tache->getDeadline()->format('d/m/Y') . ")");
                $alerteRetard->setDateAlerte(new \DateTime());

                // On ajoute cette fausse alerte à la liste pour qu'elle s'affiche en rouge
                $alertes[] = $alerteRetard;
            }
        }

        // 7. On envoie les données à la vue
        return $this->render('home_page/index.html.twig', [
            'mesure' => $derniereMesure,
            'alertes' => $alertes,
            'taches' => $taches,
            'chartData' => json_encode($historiqueData),
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
}