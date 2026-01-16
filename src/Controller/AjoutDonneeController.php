<?php

namespace App\Controller;

use App\Entity\Mesure;
use App\Entity\Aquarium;
use App\Repository\AquariumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AjoutDonneeController extends AbstractController
{
    #[Route('/ajoutDonnee', name: 'ajoutDonnee')]
    public function index(Request $request, EntityManagerInterface $em, AquariumRepository $aquariumRepo): Response
    {
        if ($request->isMethod('POST')) {
            
            // On récupère le premier aquarium disponible puisqu'il n'y a plus d'utilisateur
            $aquarium = $aquariumRepo->findOneBy([]);
            
            // Si aucun aquarium n'existe, on en crée un par défaut
            if (!$aquarium) {
                $aquarium = new Aquarium();
                $aquarium->setNom("Mon Aquarium");
                $aquarium->setTemperature(25);
                $aquarium->setVolumeLitre(100);
                $aquarium->setTypeEau("Douce");
                $aquarium->setDerniereMaj(new \DateTime());
                $aquarium->setDernierChangementEau(new \DateTime());
                $aquarium->setDescription("Créé automatiquement");
                $em->persist($aquarium);
            }

            $mesure = new Mesure();

            // Gestion de la date
            $dateForm = $request->request->get('date');
            $dateSaisie = new \DateTime($dateForm ?: 'now');
            $dateSaisie->setTime((int)date('H'), (int)date('i'), (int)date('s')); 
            
            $mesure->setDateSaisie($dateSaisie);

            // Hydratation des données classiques
            $mesure->setTemperature((float)$request->request->get('temperature'));
            $mesure->setPh((float)$request->request->get('ph'));
            $mesure->setChlore((float)$request->request->get('chlore'));
            $mesure->setGh((int)$request->request->get('gh'));
            $mesure->setKh((int)$request->request->get('kh'));

            // --- AJOUT POUR LES GRAPHIQUES ---
            // On récupère les nouveaux champs nitrites et ammonium
            $mesure->setNitrites((float)$request->request->get('nitrites'));
            $mesure->setAmmonium((float)$request->request->get('ammonium'));
            
            // On lie la mesure à l'aquarium (mais plus à l'utilisateur)
            $mesure->setAquarium($aquarium);

            $em->persist($mesure);
            $em->flush();

            // On redirige vers l'accueil
            return $this->redirectToRoute('homePage');
        }

        return $this->render('ajout_donnee/index.html.twig');
    }
}