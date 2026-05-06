<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\MesureRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'gestion_compte')]
    public function index(MesureRepository $mesureRepo): Response
    {
        $user = $this->getUser();
        $mesures = [];

        if ($user) {
            $mesures = $mesureRepo->findBy(['user' => $user], ['dateSaisie' => 'DESC']);
        }

        return $this->render('profil/index.html.twig', [
            'user'    => $user,
            'mesures' => $mesures,
        ]);
    }

    #[Route('/profil/edit', name: 'app_profil_edit')]
    public function editProfile(
        Request                $request,
        EntityManagerInterface $entityManager,
        SluggerInterface       $slugger,
        UserRepository         $userRepository,
        TokenStorageInterface  $tokenStorage,
    ): Response {
        /** @var User $sessionUser */
        $sessionUser = $this->getUser();

        // Recharge l'user depuis la BDD pour que Doctrine le track correctement
        $user = $userRepository->find($sessionUser->getId());

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile|null $avatarFile */
            $avatarFile = $form->get('avatarFile')->getData();

            if ($avatarFile) {
                $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename     = $slugger->slug($originalFilename);
                $newFilename      = $safeFilename . '-' . uniqid() . '.' . $avatarFile->guessExtension();

                try {
                    $avatarFile->move(
                        $this->getParameter('avatars_directory'),
                        $newFilename
                    );
                } catch (\Symfony\Component\HttpFoundation\File\Exception\FileException $e) {
                    $this->addFlash('error', "Erreur lors de l'upload de l'image.");
                    return $this->redirectToRoute('app_profil_edit');
                }

                $user->setAvatar($newFilename);
            }

            // Sauvegarde en BDD
            $entityManager->flush();

            // Rafraîchit le token de sécurité pour que app.user reflète les nouveaux champs
            $currentToken = $tokenStorage->getToken();
            if ($currentToken) {
                $currentToken->setUser($user);
                $tokenStorage->setToken($currentToken);
            }

            $this->addFlash('success', 'Profil mis à jour avec succès !');

            return $this->redirectToRoute('gestion_compte');
        }

        return $this->render('profil/edit.html.twig', [
            'userForm' => $form->createView(),
        ]);
    }
}