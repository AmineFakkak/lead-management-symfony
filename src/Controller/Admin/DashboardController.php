<?php

namespace App\Controller\Admin;

use App\Repository\LeadRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Entity;
use App\Entity\Lead;
use App\Entity\Project;
use App\Entity\Campaign;
use App\Entity\Interaction;
use App\Entity\Task;
use App\Entity\Tag;
use App\Entity\User;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    private LeadRepository $leadRepository;

    public function __construct(LeadRepository $leadRepository)
    {
        $this->leadRepository = $leadRepository;
    }

    public function index(): Response
    {
        $totalLeads = $this->leadRepository->count([]);
        $leadsParStatut = $this->leadRepository->countByStatut();
        $leadsParEntite = $this->leadRepository->countByEntite();
        
        // Nouveaux indicateurs
        $leadsGagnes = $this->leadRepository->count(['status' => 'Gagné']);
        $leadsPerdus = $this->leadRepository->count(['status' => 'Perdu']);
        $leadsEnCours = $this->leadRepository->countByStatus(['Nouveau','Contacté','Qualifié','Proposition','Négociation']);
        
        // Évolution mensuelle
        $evolutionMois = $this->leadRepository->countByMonth();
        
        // Top sources
        $topSources = $this->leadRepository->countBySource(5);

        return $this->render('admin/dashboard.html.twig', [
            'totalLeads' => $totalLeads,
            'leadsParStatut' => $leadsParStatut,
            'leadsParEntite' => $leadsParEntite,
            'leadsGagnes' => $leadsGagnes,
            'leadsPerdus' => $leadsPerdus,
            'leadsEnCours' => $leadsEnCours,
            'evolutionMois' => $evolutionMois,
            'topSources' => $topSources,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Gestion des Leads - Flash Ingénierie')
            ->setFaviconPath('favicon.ico')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Tableau de bord', 'fa fa-home');
        yield MenuItem::linkToCrud('Entités', 'fa fa-building', Entity::class);
        yield MenuItem::linkToCrud('Leads', 'fa fa-users', Lead::class);
        yield MenuItem::linkToCrud('Projets', 'fa fa-folder', Project::class);
        yield MenuItem::linkToCrud('Campagnes', 'fa fa-bullhorn', Campaign::class);
        yield MenuItem::linkToCrud('Interactions', 'fa fa-comments', Interaction::class);
        yield MenuItem::linkToCrud('Tâches', 'fa fa-tasks', Task::class);
        yield MenuItem::linkToCrud('Tags', 'fa fa-tags', Tag::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-user', User::class);
    }
}