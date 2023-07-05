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

class ProfilController extends AbstractController
{
    #[Route('/profil/{id}', name: 'app_profil', priority: -1)]
    public function index(
        Participant $participant
    ): Response
    {
        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
            'userData' => $participant
        ]);
    }

    #[Route('/profil/edit', name: 'app_profil_edit')]
    public function edit(
        Request                $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher
    ): Response
    {
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
