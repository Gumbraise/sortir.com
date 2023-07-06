<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ProfileEditFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profil')]
class ProfilController extends AbstractController
{
    #[Route('/', name: 'app_profil_own')]
    public function own(): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('app_login');
        }

        return $this->redirectToRoute('app_profil', ['id' => $this->getUser()->getId()]);
    }


    #[Route('/{id}', name: 'app_profil', priority: -1)]
    public function index(
        Participant $participant
    ): Response
    {
        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
            'userData' => $participant
        ]);
    }

    #[Route('/edit', name: 'app_profil_edit')]
    public function edit(
        Request                     $request,
        EntityManagerInterface      $entityManager,
        UserPasswordHasherInterface $userPasswordHasher
    ): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('app_login');
        }

        /** @var Participant $user */
        $user = $this->getUser();
        $form = $this->createForm(ProfileEditFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre profil a bien été modifié.');
            return $this->redirectToRoute('app_profil_edit');
        }

        return $this->render('profil/edit.html.twig', [
            'form' => $form
        ]);
    }

}
