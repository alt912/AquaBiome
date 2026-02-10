<?php

namespace App\Controller;

use App\Entity\Mesure;
use App\Entity\MesureHistorique;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ModificationDonneeController extends AbstractController
{
    #[Route('/mesure/edit/{id}', name: 'app_mesure_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(Mesure $mesure, Request $request, EntityManagerInterface $em): Response
    {
        // 1. Vérification que c'est bien la mesure de l'utilisateur (ou admin)
        if ($mesure->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas modifier cette mesure.");
        }

        if ($request->isMethod('POST')) {
            $changements = [];

            // 2. Comparaison et Mise à jour
            $oldTemp = $mesure->getTemperature();
            $newTemp = (float) $request->request->get('temperature');
            if ($oldTemp !== $newTemp) {
                $mesure->setTemperature($newTemp);
                $changements[] = "Temp: $oldTemp -> $newTemp °C";
            }

            $oldPh = $mesure->getPh();
            $newPh = (float) $request->request->get('ph');
            if ($oldPh !== $newPh) {
                $mesure->setPh($newPh);
                $changements[] = "pH: $oldPh -> $newPh";
            }

            $oldGh = $mesure->getGh();
            $newGh = (int) $request->request->get('gh');
            if ($oldGh !== $newGh) {
                $mesure->setGh($newGh);
                $changements[] = "GH: $oldGh -> $newGh";
            }

            $oldKh = $mesure->getKh();
            $newKh = (int) $request->request->get('kh');
            if ($oldKh !== $newKh) {
                $mesure->setKh($newKh);
                $changements[] = "KH: $oldKh -> $newKh";
            }

            $oldNitrites = $mesure->getNitrites();
            $newNitrites = (float) $request->request->get('nitrite');
            if ($oldNitrites !== $newNitrites) {
                $mesure->setNitrites($newNitrites);
                $changements[] = "NO2: $oldNitrites -> $newNitrites";
            }

            $oldAmmonium = $mesure->getAmmonium();
            $newAmmonium = (float) $request->request->get('ammonium');
            if ($oldAmmonium !== $newAmmonium) {
                $mesure->setAmmonium($newAmmonium);
                $changements[] = "NH4: $oldAmmonium -> $newAmmonium";
            }

            // 3. Enregistrement de l'historique SI changements
            if (!empty($changements)) {
                $historique = new MesureHistorique();
                $historique->setMesure($mesure);
                $historique->setUser($this->getUser());
                $historique->setDateAction(new \DateTime());
                $historique->setAction("MODIFICATION");
                $historique->setDetails(implode(" | ", $changements));
                
                $em->persist($historique);
                $em->flush();

                $this->addFlash('success', 'Mesure corrigée avec succès !');
            } else {
                $this->addFlash('info', 'Aucune modification détectée.');
            }

            return $this->redirectToRoute('gestion_compte'); // Retour au profil
        }

        return $this->render('modification_donnee/index.html.twig', [
            'mesure' => $mesure,
        ]);
    }
}
