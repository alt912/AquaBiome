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
            $ghSaisi = (float)$request->request->get('gh');

            // --- SÉCURITÉ SERVEUR ---
            if ($ghSaisi > 100) {
                $this->addFlash('error', 'Le GH est faux (trop élevé). La valeur maximale autorisée est 100.');
                return $this->render('ajout_donnee/index.html.twig');
            }

            $aquarium = $aquariumRepo->findOneBy([]);
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
            $dateForm = $request->request->get('date');
            $dateSaisie = new \DateTime($dateForm ?: 'now');
            $dateSaisie->setTime((int)date('H'), (int)date('i'), (int)date('s')); 
            
            $mesure->setDateSaisie($dateSaisie);
            $mesure->setTemperature((float)$request->request->get('temperature'));
            $mesure->setPh((float)$request->request->get('ph'));
            $mesure->setChlore((float)$request->request->get('chlore'));
            $mesure->setGh($ghSaisi);
            $mesure->setKh((int)$request->request->get('kh'));
            
            // On s'assure que le nom correspond au formulaire ('nitrite' dans le twig)
            $mesure->setNitrites((float)$request->request->get('nitrite'));
            $mesure->setAmmonium((float)$request->request->get('ammonium'));
            
            $mesure->setAquarium($aquarium);

            $em->persist($mesure);
            $em->flush();

            $this->addFlash('success', 'Mesures enregistrées avec succès !');
            return $this->redirectToRoute('homePage');
        }

        return $this->render('ajout_donnee/index.html.twig');
    }
}