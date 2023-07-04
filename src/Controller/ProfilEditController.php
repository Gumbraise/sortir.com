<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ProfileEditFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class ProfilEditController extends AbstractController
{
    #[Route('/profilEdit', name: 'app_profil_edit')]
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasher,  UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        /** @var Participant $user */
        $user = $this->getUser();
        $form = $this->createForm(ProfileEditFormType::class,$user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setNom($form->get('nom')->getData());

            $entityManager->persist($user);
            $entityManager->flush();


            return $this->redirectToRoute('app_profil_edit');
        }

        return $this->render('profilEdit/index.html.twig', [
            'controller_name' => 'ProfilEditController',
            'form' => $form
        ]);
    }
}
