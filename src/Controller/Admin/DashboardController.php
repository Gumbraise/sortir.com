<?php

namespace App\Controller\Admin;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        return $this->render('admin/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Sortir');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Sorties');
        yield MenuItem::subMenu('Sorties', 'fa fa-map-marker-alt')->setSubItems([
            MenuItem::linkToCrud('Toutes les sorties', 'fa fa-map-marker-alt', Sortie::class),
            MenuItem::linkToCrud('Ajouter une sortie', 'fa fa-plus', Sortie::class)
                ->setAction('new'),
        ]);
        yield MenuItem::subMenu('Lieux', 'fa fa-map-marker-alt')->setSubItems([
            MenuItem::linkToCrud('Tous les lieux', 'fa fa-map-marker-alt', Lieu::class),
            MenuItem::linkToCrud('Ajouter un lieu', 'fa fa-plus', Lieu::class)
                ->setAction('new'),
        ]);
        yield MenuItem::subMenu('Ville', 'fa fa-map-marker-alt')->setSubItems([
            MenuItem::linkToCrud('Toutes les villes', 'fa fa-map-marker-alt', Ville::class),
            MenuItem::linkToCrud('Ajouter une ville', 'fa fa-plus', Ville::class)
                ->setAction('new'),
        ]);
        yield MenuItem::linkToCrud('Etats des sorties', 'fa fa-check-square', Etat::class);

        yield MenuItem::section('Utilisateurs');
        yield MenuItem::subMenu('Participants', 'fa fa-users')->setSubItems([
            MenuItem::linkToCrud('Tous les participants', 'fa fa-user', Participant::class),
            MenuItem::linkToCrud('Ajouter un prticipant', 'fa fa-plus', Participant::class)
                ->setAction('new'),
        ]);
        yield MenuItem::subMenu('Campus', 'fa fa-school')->setSubItems([
            MenuItem::linkToCrud('Tous les participants', 'fa fa-school', Campus::class),
            MenuItem::linkToCrud('Ajouter un prticipant', 'fa fa-plus', Campus::class)
                ->setAction('new'),
        ]);
    }
}
