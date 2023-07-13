<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\ExcelType;
use App\Form\SearchSortieType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Utils\SendMail;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sorties')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'app_sortie_index', methods: ['GET'])]
    public function index(
        Request          $request,
        SortieRepository $sortieRepository,
        EtatRepository   $etatRepository,
    ): Response
    {
        $form = $this->createForm(SearchSortieType::class);
        $form->handleRequest($request);

        $name = null;
        $startDate = new \DateTimeImmutable('now');
        $endDate = null;
        $checkboxs = [
            "isOrganisateur" => false,
            "isInscrit" => true,
            "isNotInscrit" => true,
            "isPast" => false,
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form->get('nom')->getData();
            $startDate = $form->get('dateStart')->getData();
            $endDate = $form->get('dateEnd')->getData();
            $checkboxs = [
                "isOrganisateur" => $form->get('isOrganisateur')->getData(),
                "isInscrit" => $form->get('isInscrit')->getData(),
                "isNotInscrit" => $form->get('isNotInscrit')->getData(),
                "isPast" => $form->get('isPast')->getData(),
            ];
        }

        $sorties = $sortieRepository->search(
            $this->getUser(),
            $name,
            $startDate,
            $endDate,
            $checkboxs,
            $etatRepository->findByLibelle("Ouverte")
        );

        return $this->render('sortie/index.html.twig', [
            'sorties' => $sorties,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/new', name: 'app_sortie_new', methods: ['GET', 'POST'])]
    public function new(
        Request          $request,
        SortieRepository $sortieRepository,
        EtatRepository   $etatRepository
    ): Response
    {
        $sortie = new Sortie();

        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($this->getUser()->getCampus()){
                $sortie->setCampus($this->getUser()->getCampus());
            }
            $sortie->setOrganisateur($this->getUser());
            $sortie->setEtat($etatRepository->findByLibelle('Créée'));
            $sortieRepository->save($sortie, true);

            $this->addFlash('success', 'Votre sortie a bien été créée !');

            return $this->redirectToRoute('app_sortie_show', ["id" => $sortie->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sortie_show', methods: ['GET'])]
    public function show(
        Sortie                 $sortie,
        ParticipantRepository  $participantRepository,
        SendMail               $sendMail,
        EntityManagerInterface $entityManager,
        Request                $request
    ): Response
    {
        if (
            !$sortie->getParticipants()->contains($this->getUser()) &&
            !$sortie->getCampus()->getParticipants()->contains($this->getUser()) &&
            $sortie->getOrganisateur() !== $this->getUser() &&
            !$this->isGranted('ROLE_ADMIN')
        ) {
            return $this->redirectToRoute('app_sortie_index');
        }

        if ($sortie->getDateHeureDebut() < new \DateTimeImmutable("-1 month")) {
            $this->addFlash('error', 'La sortie est trop ancienne pour être affichée');
            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        if (
            $sortie->getOrganisateur() === $this->getUser() ||
            $this->isGranted('ROLE_ADMIN')
        ) {
            $excelForm = $this->createForm(ExcelType::class);
            $excelForm->handleRequest($request);

            if ($excelForm->isSubmitted() && $excelForm->isValid()) {

                $spreadsheet = IOFactory::load($excelForm->get('excel_file')->getData());
                $worksheet = $spreadsheet->getActiveSheet();
                $data = [];
                foreach ($worksheet->getRowIterator() as $row) {
                    $rowData = [];
                    foreach ($row->getCellIterator() as $cell) {
                        $rowData[] = $cell->getValue();
                    }
                    $data[] = $rowData;
                }

                // Insertion des données dans la base de données
                foreach ($data as $rowData) {
                    $newParticipant = $participantRepository->findOneBy(['pseudo' => $rowData[0]]);

                    if ($newParticipant) {
                        $sortie->addParticipant($newParticipant);

                        try {
                            $entityManager->persist($newParticipant);
                            $entityManager->flush();

                            $sendMail->TemplatedEmail(
                                $newParticipant->getEmail(),
                                'no-reply@sortir.com',
                                'Votre inscription à la sortie ' . $sortie . ' !',
                                '_mailer/sortie/inscription.html.twig',
                                ['sortie' => $sortie]
                            );
                        } catch (\Exception $e) {
                            $this->addFlash('error', 'Une erreur est survenue lors de l\'inscription');
                        } catch (TransportExceptionInterface $e) {
                            $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi des mails');
                        }
                    } else {
                        $this->addFlash('error', "$rowData[0] n'est pas inscrit sur Sortir.com");
                    }
                }
                return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
            'excelForm' => $excelForm->createView(),
        ]);
    }

    #[Route('/{id}/inscrire', name: 'app_sortie_inscription', methods: ['POST'])]
    public function inscrire(
        Sortie                 $sortie,
        EntityManagerInterface $entityManager,
        SendMail               $sendMail
    ): Response
    {
        if (
            $sortie->getParticipants()->contains($this->getUser()) ||
            $sortie->getEtat()->getLibelle() !== 'Ouverte' ||
            $sortie->getNbInscriptionMax() <= $sortie->getParticipants()->count() ||
            $sortie->getDateLimiteInscription() < new \DateTimeImmutable() ||
            $sortie->getDateHeureDebut() < new \DateTimeImmutable("-1 month")
        ) {
            $this->addFlash('error', 'La date limite d\'inscription est dépassée');
            return $this->redirectToRoute('app_sortie_show', ["id" => $sortie->getId()], Response::HTTP_SEE_OTHER);
        }

        $sortie->addParticipant($this->getUser());
        try {
            $entityManager->flush();

            $sendMail->TemplatedEmail(
                $this->getUser()->getEmail(),
                'no-reply@sortir.com',
                'Votre inscription à la sortie ' . $sortie . ' !',
                '_mailer/sortie/inscription.html.twig',
                ['sortie' => $sortie]
            );
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de l\'inscription');
        } catch (TransportExceptionInterface $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi des mails');
        }

        return $this->redirectToRoute('app_sortie_show', ["id" => $sortie->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/desinscrire', name: 'app_sortie_desinscription', methods: ['POST'])]
    public function desinscrire(
        Sortie                 $sortie,
        EntityManagerInterface $entityManager,
        SendMail               $sendMail
    ): Response
    {
        $sortie->removeParticipant($this->getUser());
        try {
            $entityManager->flush();

            $sendMail->TemplatedEmail(
                $this->getUser()->getEmail(),
                'no-reply@sortir.com',
                'Votre désinscription à la sortie ' . $sortie . ' !',
                '_mailer/sortie/desinscription.html.twig',
                ['sortie' => $sortie]
            );
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la désinscription');
        } catch (TransportExceptionInterface $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi des mails');
        }

        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/edit', name: 'app_sortie_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request          $request,
        Sortie           $sortie,
        SortieRepository $sortieRepository,
        SendMail         $sendMail
    ): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $sortieRepository->save($sortie, true);

                $sendMail->TemplatedEmail(
                    $sortie->getOrganisateur()->getEmail(),
                    'no-reply@sortir.com',
                    'Votre sortie ' . $sortie . ' a bien été modifiée !',
                    '_mailer/sortie/updated_organisateur.html.twig',
                    ['sortie' => $sortie]
                );

                foreach ($sortie->getParticipants() as $participant) {
                    $sendMail->TemplatedEmail(
                        $participant->getEmail(),
                        $sortie->getOrganisateur()->getEmail(),
                        'La sortie ' . $sortie . ' a été modifiée !',
                        '_mailer/sortie/updated_participant.html.twig',
                        ['sortie' => $sortie]
                    );
                }

                $this->addFlash('success', 'Votre sortie a bien été modifiée !');
            } catch (\Exception $e) {
                $this->addFlash('error', "Une erreur s'est produite.");
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('error', "Une erreur s'est produite lors de l'envoi des mails.");
            }


            return $this->redirectToRoute('app_sortie_show', ["id" => $sortie->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/{id}/publier', name: 'app_sortie_publish', methods: ['GET', 'POST'])]
    public function publish(
        Sortie           $sortie,
        SortieRepository $sortieRepository,
        EtatRepository   $etatRepository
    ): Response
    {
        $sortie->setEtat($etatRepository->findByLibelle('Ouverte'));
        $sortieRepository->save($sortie, true);

        $this->addFlash('success', 'Votre sortie a bien été publiée !');

        return $this->redirectToRoute('app_sortie_show', ["id" => $sortie->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/annuler', name: 'app_sortie_annuler', methods: ['POST'])]
    public function annuler(
        Request          $request,
        Sortie           $sortie,
        SortieRepository $sortieRepository,
        EtatRepository   $etatRepository,
        SendMail         $sendMail
    )
    {
        if (
            !$sortie->getParticipants()->contains($this->getUser()) &&
            $sortie->getOrganisateur() !== $this->getUser() &&
            !$this->isGranted('ROLE_ADMIN')
        ) {
            return $this->redirectToRoute('app_sortie_index');
        }

        try {
            $sortie->setEtat($etatRepository->findByLibelle("Annulée"));
            $sortie->setRaisonsAnnulation($request->request->get('raisonsAnnulation'));
            $sortieRepository->save($sortie, true);

            $sendMail->TemplatedEmail(
                $sortie->getOrganisateur()->getEmail(),
                'no-reply@sortir.com',
                'Votre sortie ' . $sortie . ' a bien été annulée !',
                '_mailer/sortie/canceled_organisateur.html.twig',
                ['sortie' => $sortie]
            );
            foreach ($sortie->getParticipants() as $participant) {
                $sendMail->TemplatedEmail(
                    $participant->getEmail(),
                    $sortie->getOrganisateur()->getEmail(),
                    'La sortie ' . $sortie . ' a été annulée !',
                    '_mailer/sortie/canceled_participant.html.twig',
                    ['sortie' => $sortie]
                );
            }

            $this->addFlash('success', "La sortie $sortie a bien été annulée");
        } catch (\Exception $e) {
            $this->addFlash('error', "Une erreur s'est produite.");
        } catch (TransportExceptionInterface $e) {
            $this->addFlash('error', "Une erreur s'est produite lors de l'envoi des mails.");
        }
    }
}
