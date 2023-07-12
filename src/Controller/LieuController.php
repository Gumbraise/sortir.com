<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/lieu')]
class LieuController extends AbstractController
{
    #[Route('/add', name: 'app_lieu_add')]
    public function index(
        Request $request,
        LieuRepository $lieuRepository
    ): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->redirectToRoute('app_login');
        }

        $lieu = new Lieu();
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $lieuRepository->save($lieu, true);
            return $this->redirectToRoute('app_sortie_new');
        }

        return $this->render('lieu/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
