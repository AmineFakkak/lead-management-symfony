<?php

namespace App\Controller\Admin;

use App\Repository\LeadRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\CrudMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\DashboardMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard; // <-- EA5
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Entity;
use App\Entity\Lead;
use App\Entity\Project;
use App\Entity\Campaign;
use App\Entity\Interaction;
use App\Entity\Task;
use App\Entity\Tag;
use App\Entity\User;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')] // <-- Remplace #[Route]
class DashboardController extends AbstractDashboardController
{
    private LeadRepository $leadRepository;

    public function __construct(LeadRepository $leadRepository)
    {
        $this->leadRepository = $leadRepository;
    }
 public function configureAssets(): Assets
    {
        return Assets::new()
            // Google Fonts
            ->addCssFile('https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,800')
            
            // Font Awesome
            ->addCssFile('https://kit.fontawesome.com/42d5adcbca.js')
            
            // Nucleo Icons
            ->addCssFile('assets/css/nucleo-icons.css')
            ->addCssFile('assets/css/nucleo-svg.css')
            
            // Soft UI Dashboard CSS
            ->addCssFile('assets/css/soft-ui-dashboard.min.css')
            
            // Custom CSS for EasyAdmin integration
            ->addCssFile('css/easyadmin-soft-ui.css')
            
            // JavaScript files
            ->addJsFile('assets/js/core/popper.min.js')
            ->addJsFile('assets/js/core/bootstrap.min.js')
            ->addJsFile('assets/js/plugins/perfect-scrollbar.min.js')
            ->addJsFile('assets/js/plugins/smooth-scrollbar.min.js')
            ->addJsFile('assets/js/plugins/chartjs.min.js')
            ->addJsFile('assets/js/soft-ui-dashboard.min.js');
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->overrideTemplate('layout', 'admin/layout.html.twig');
        }
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Gestion des Leads - Flash Ingénierie');
    }
    public function index(): Response
    {
        $totalLeads = $this->leadRepository->count([]);
        $leadsParStatut = $this->leadRepository->countByStatut();
        $leadsParEntite = $this->leadRepository->countByEntite();
        $leadsGagnes = $this->leadRepository->count(['status' => 'Gagné']);
        $leadsPerdus = $this->leadRepository->count(['status' => 'Perdu']);
        $leadsEnCours = $this->leadRepository->countByStatus(['Nouveau','Contacté','Qualifié','Proposition','Négociation']);
        $evolutionMois = $this->leadRepository->countByMonth();
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

   

    public function configureMenuItems(): iterable
{
    yield new DashboardMenuItem('Tableau de bord', 'fa fa-home');

    yield new CrudMenuItem('Entités', 'fa fa-building', Entity::class);
    yield new CrudMenuItem('Leads', 'fa fa-users', Lead::class);
    yield new CrudMenuItem('Projets', 'fa fa-folder', Project::class);
    yield new CrudMenuItem('Campagnes', 'fa fa-bullhorn', Campaign::class);
    yield new CrudMenuItem('Interactions', 'fa fa-comments', Interaction::class);
    yield new CrudMenuItem('Tâches', 'fa fa-tasks', Task::class);
    yield new CrudMenuItem('Tags', 'fa fa-tags', Tag::class);
    yield new CrudMenuItem('Utilisateurs', 'fa fa-user', User::class);
}

}
