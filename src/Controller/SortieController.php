<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SearchSortieType;
use App\Form\SortieType;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sorties')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'app_sortie_index', methods: ['GET'])]
    public function index(
        Request          $request,
        SortieRepository $sortieRepository,
        CampusRepository $campusRepository,
    ): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SearchSortieType::class, $sortie);
        $form->handleRequest($request);

        $campus = [];
        $nom = null;
        $dateHeureDebut = (new \DateTimeImmutable());
        $dateLimiteInscription = (new \DateTimeImmutable("9999/12/31"));

        dump($dateHeureDebut, $dateLimiteInscription);
        if ($form->isSubmitted() && $form->isValid()) {
            $campus = $form->get('campus')->getData() ?? $form->get('campus')->getData();
            $dateHeureDebut = $form->get('dateHeureDebut')->getData() ?? $form->get('dateHeureDebut')->getData();
            $dateLimiteInscription = $form->get('dateLimiteInscription')->getData() ?? $form->get('dateLimiteInscription')->getData();
            $nom = $form->get('nom')->getData() ?? $form->get('nom')->getData();
        }
        dump($dateHeureDebut, $dateLimiteInscription);
        $sorties = $sortieRepository->search(
            $campus,
            $nom,
            $dateHeureDebut,
            $dateLimiteInscription,
        );

        return $this->render('sortie/index.html.twig', [
            'sorties' => $sorties,
            'campuses' => $campusRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'app_sortie_new', methods: ['GET', 'POST'])]
    public function new(
        Request          $request,
        SortieRepository $sortieRepository
    ): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortieRepository->save($sortie, true);

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sortie_show', methods: ['GET'])]
    public function show(Sortie $sortie): Response
    {
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    #[Route('/{id}/inscrire', name: 'app_sortie_inscription', methods: ['GET', 'POST'])]
    public function inscrire(
        Sortie $sortie,
        EntityManagerInterface $entityManager,
    ): Response
    {
        $sortie->addParticipant($this->getUser());
        try {
            $entityManager->flush();
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de l\'inscription');
        }

        return $this->redirectToRoute('app_sortie_show', ["id" => $sortie->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/desinscrire', name: 'app_sortie_desinscription', methods: ['GET', 'POST'])]
    public function desinscrire(
        Sortie $sortie,
        EntityManagerInterface $entityManager,
    ): Response
    {
        $sortie->removeParticipant($this->getUser());
        try {
            $entityManager->flush();
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la désinscription');
        }

        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/edit', name: 'app_sortie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortieRepository->save($sortie, true);

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sortie_delete', methods: ['POST'])]
    public function delete(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $sortie->getId(), $request->request->get('_token'))) {
            $sortieRepository->remove($sortie, true);
        }

        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }
}
