<?php

namespace App\Controller\Admin;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;

class TaskCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Task::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->overrideTemplate('layout', 'admin/layout.html.twig')
            ->setEntityLabelInSingular('Tâche')
            ->setEntityLabelInPlural('Tâches')
            ->setPageTitle('index', 'Liste des tâches')
            ->setPageTitle('new', 'Créer une tâche')
            ->setPageTitle('edit', 'Modifier la tâche')
            ->setDefaultSort(['dueDate' => 'ASC'])
            ->setPaginatorUseOutputWalkers(false);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Ajouter une tâche')->setIcon('fas fa-plus')->setCssClass('btn btn-primary');
            })
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setLabel('Modifier')->setIcon('fas fa-pen')->setCssClass('btn btn-sm btn-secondary');
            })
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
                return $action->setLabel('Voir')->setIcon('fas fa-eye')->setCssClass('btn btn-sm btn-secondary');
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setLabel('Supprimer')->setIcon('fas fa-trash')->setCssClass('btn btn-sm btn-danger-soft');
            })
            ->update(Crud::PAGE_INDEX, Action::BATCH_DELETE, function (Action $action) {
                return $action->setLabel('Supprimer la sélection')->setIcon('fas fa-trash')->setCssClass('btn btn-danger');
            })
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN, function (Action $action) {
                return $action->setLabel('Enregistrer')->setCssClass('btn btn-primary');
            })
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE, function (Action $action) {
                return $action->setLabel('Enregistrer et continuer')->setCssClass('btn btn-secondary');
            })
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_RETURN, function (Action $action) {
                return $action->setLabel('Créer la tâche')->setCssClass('btn btn-primary');
            })
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER, function (Action $action) {
                return $action->setLabel('Créer et ajouter une autre')->setCssClass('btn btn-secondary');
            })
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE]);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title', 'Titre')->setRequired(true),
            TextareaField::new('description', 'Description')->hideOnIndex(),
            DateTimeField::new('dueDate', 'Échéance')
                ->setRequired(false)
                ->setFormat('dd/MM/yyyy HH:mm'),
            ChoiceField::new('priority', 'Priorité')
                ->setChoices([
                    'Basse'   => 'Basse',
                    'Normale' => 'Normale',
                    'Haute'   => 'Haute',
                    'Urgente' => 'Urgente',
                ])
                ->setRequired(true),
            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'À faire'  => 'À faire',
                    'En cours' => 'En cours',
                    'Terminée' => 'Terminée',
                    'Annulée'  => 'Annulée',
                ])
                ->setRequired(true),
            AssociationField::new('lead', 'Lead associé')
                ->setRequired(false)
                ->setFormTypeOptions(['choice_label' => 'fullName']),
            AssociationField::new('assignedTo', 'Assigné à')
                ->setRequired(false)
                ->setFormTypeOptions(['choice_label' => 'fullName']),
            DateTimeField::new('createdAt', 'Créé le')->hideOnForm()->setFormat('dd/MM/yyyy HH:mm'),
            DateTimeField::new('completedAt', 'Terminée le')->hideOnForm()->setFormat('dd/MM/yyyy HH:mm'),
        ];
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $user = $this->getUser();

        if (!$user) {
            return $qb->andWhere('1 = 0');
        }

        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true)) {
            return $qb;
        }

        // Tasks assigned directly to user OR tasks on leads assigned to user
        $qb->leftJoin('entity.lead', 'l')
           ->andWhere('entity.assignedTo = :user OR l.assignedTo = :user')
           ->setParameter('user', $user);

        return $qb;
    }
}