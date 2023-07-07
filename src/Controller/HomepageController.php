<?php

namespace App\Controller;

use App\Entity\Participant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(): Response
    {
        if($this->getUser() != null){
            $user = $this->getUser();
            if(!$user->isActif()){
                $this->addFlash('success', 'Erreur : Votre compte à été désactivé, veuillez contacter un responsable.');
                return $this->redirectToRoute('app_logout');
            }
        }
        return $this->render('homepage/index.html.twig', [
        ]);
    }
}
