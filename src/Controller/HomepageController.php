<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(
        SortieRepository      $sortieRepository,
        ParticipantRepository $participantRepository,
        CampusRepository      $campusRepository,
    ): Response
    {
        return $this->render('homepage/index.html.twig', [
            'sorties' => $sortieRepository->findAll(),
            'participants' => $participantRepository->findAll(),
            'campus' => $campusRepository->findAll(),
        ]);
    }
}
