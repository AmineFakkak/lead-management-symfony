<?php

namespace App\Controller\Admin;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;

class ProjectCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Project::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->overrideTemplate('layout', 'admin/layout.html.twig')
            ->setEntityLabelInSingular('Projet')
            ->setEntityLabelInPlural('Projets')
            ->setPageTitle('index', 'Liste des projets')
            ->setPageTitle('new', 'Créer un projet')
            ->setPageTitle('edit', 'Modifier le projet')
            ->setDefaultSort(['name' => 'ASC'])
            ->setPaginatorUseOutputWalkers(false);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Ajouter un projet')->setIcon('fas fa-plus')->setCssClass('btn btn-primary');
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
                return $action->setLabel('Créer le projet')->setCssClass('btn btn-primary');
            })
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER, function (Action $action) {
                return $action->setLabel('Créer et ajouter un autre')->setCssClass('btn btn-secondary');
            })
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE]);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Nom du projet')->setRequired(true),
            TextareaField::new('description', 'Description')->hideOnIndex(),
            DateField::new('startDate', 'Date de début')->setRequired(false),
            DateField::new('endDate', 'Date de fin')->setRequired(false),
            MoneyField::new('budget', 'Budget')
                ->setCurrency('MAD')
                ->setStoredAsCents(false)
                ->setRequired(false),
            IntegerField::new('targetLeads', 'Objectif leads')->setRequired(false),
            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'En cours' => 'En cours',
                    'Terminé'  => 'Terminé',
                    'Annulé'   => 'Annulé',
                ])
                ->setRequired(true),
            AssociationField::new('entity', 'Entité')
                ->setRequired(true)
                ->setFormTypeOptions(['choice_label' => 'name']),
            DateTimeField::new('createdAt', 'Créé le')->hideOnForm(),
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

        if (!$user instanceof User) {
            return $qb->andWhere('1 = 0');
        }

        $entity = $user->getEntity();
        if (!$entity) {
            return $qb->andWhere('1 = 0');
        }

        $qb->andWhere('entity.entity = :entity')
           ->setParameter('entity', $entity);

        return $qb;
    }
}