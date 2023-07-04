<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil/{id}', name: 'app_profil')]
    public function index($id,ParticipantRepository $participantRepository): Response
    {
        $user = $participantRepository->find($id);
        $userData = [
            'image' => $user->getProfilePictureName(),
            'pseudo' => $user->getPseudo(),
            'prenom' => $user->getPrenom(),
            'nom' => $user->getNom(),
            'telephone' => $user->getTelephone(),
            'email' => $user->getEmail(),
            'campus' => $user->getCampus()?->getNom(),
        ];
        //dd($userData);
        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
            'userData' => $userData
        ]);
    }
}
