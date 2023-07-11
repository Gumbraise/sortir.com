<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/ville')]
class VilleController extends AbstractController
{
    #[Route('/add', name: 'app_ville_add')]
    public function index(
        Request $request,
        VilleRepository $villeRepository
    ): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->redirectToRoute('app_login');
        }

        $ville = new Ville();
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $villeRepository->save($ville, true);
            return $this->redirectToRoute('app_lieu_add');
        }

        return $this->render('ville/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
