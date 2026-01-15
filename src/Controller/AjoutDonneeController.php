<?php

namespace App\Controller;

use App\Entity\Mesure;
use App\Entity\Aquarium;
use App\Repository\AquariumRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AjoutDonneeController extends AbstractController
{
    #[Route('/ajoutDonnee', name: 'ajoutDonnee')]
    public function index(Request $request, EntityManagerInterface $em, AquariumRepository $aquariumRepo, UserRepository $userRepo): Response
    {
        if ($request->isMethod('POST')) {
            $user = $userRepo->find(1);
            
            if (!$user) {
                return new Response("Erreur : L'utilisateur avec l'ID 1 n'existe pas.");
            }

            $aquarium = $aquariumRepo->findOneBy(['utilisateur' => $user]);
            
            if (!$aquarium) {
                $aquarium = new Aquarium();
                $aquarium->setNom("Mon Aquarium");
                $aquarium->setUtilisateur($user);
                $aquarium->setTemperature(25);
                $aquarium->setVolumeLitre(100);
                $aquarium->setTypeEau("Douce");
                $aquarium->setDerniereMaj(new \DateTime());
                $aquarium->setDernierChangementEau(new \DateTime());
                $aquarium->setDescription("Créé automatiquement");
                $em->persist($aquarium);
            }

            $mesure = new Mesure();

            // --- MODIFICATION ICI ---
            // On récupère la date du formulaire, mais on lui ajoute l'HEURE actuelle 
            // pour que le tri DESC dans l'accueil fonctionne parfaitement.
            $dateForm = $request->request->get('date');
            $dateSaisie = new \DateTime($dateForm ?: 'now');
            $dateSaisie->setTime((int)date('H'), (int)date('i'), (int)date('s')); 
            
            $mesure->setDateSaisie($dateSaisie);
            // ------------------------

            $mesure->setTemperature((float)$request->request->get('temperature'));
            $mesure->setPh((float)$request->request->get('ph'));
            $mesure->setChlore((float)$request->request->get('chlore'));
            $mesure->setGh((int)$request->request->get('gh'));
            $mesure->setKh((int)$request->request->get('kh'));
            
            $mesure->setUtilisateur($user);
            $mesure->setAquarium($aquarium);

            $em->persist($mesure);
            $em->flush();

            // On redirige vers l'accueil
            return $this->redirectToRoute('homePage');
        }

        return $this->render('ajout_donnee/index.html.twig');
    }
}