<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'gestion_compte')]

    //#[IsGranted('ROLE_USER')]
    public function index(\App\Repository\MesureRepository $mesureRepo): Response
    {
        // Récupération de l'utilisateur connecté
        $user = $this->getUser();
        $mesures = [];

        if ($user) {
            $mesures = $mesureRepo->findBy(['user' => $user], ['dateSaisie' => 'DESC']);
        }

        return $this->render('profil/index.html.twig', [
            'user' => $user, 
            'mesures' => $mesures,
        ]);
    }

    #[Route('/profil/edit', name: 'app_profil_edit')]
    //#[IsGranted('ROLE_USER')]
    public function editProfile(\Symfony\Component\HttpFoundation\Request $request, \Doctrine\ORM\EntityManagerInterface $entityManager, \Symfony\Component\String\Slugger\SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(\App\Form\UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $avatarFile */
            $avatarFile = $form->get('avatarFile')->getData();

            // Si un fichier est uploadé
            if ($avatarFile) {
                $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                // On inclut le nom du fichier dans l'URL de manière sécurisée
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$avatarFile->guessExtension();

                // On déplace le fichier dans le répertoire où sont stockés les avatars
                try {
                    $avatarFile->move(
                        $this->getParameter('avatars_directory'),
                        $newFilename
                    );
                } catch (\Symfony\Component\HttpFoundation\File\Exception\FileException $e) {
                    // ... gérer l'exception si quelque chose se passe mal pendant l'upload
                    $this->addFlash('error', "Une erreur est survenue lors de l'upload de l'image.");
                    return $this->redirectToRoute('app_profil_edit');
                }

                // On met à jour la propriété 'avatar' pour stocker le nom du fichier PDF
                // au lieu de son contenu
                $user->setAvatar($newFilename);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès !');

            return $this->redirectToRoute('gestion_compte');
        }

        return $this->render('profil/edit.html.twig', [
            'userForm' => $form->createView(),
        ]);
    }
}