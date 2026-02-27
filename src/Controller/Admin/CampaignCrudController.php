<?php

namespace App\Controller\Admin;

use App\Entity\Campaign;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;

class CampaignCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Campaign::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->overrideTemplate('layout', 'admin/layout.html.twig')
            ->setEntityLabelInSingular('Campagne')
            ->setEntityLabelInPlural('Campagnes')
            ->setPageTitle('index', 'Liste des campagnes')
            ->setPageTitle('new', 'Créer une campagne')
            ->setPageTitle('edit', 'Modifier la campagne')
            ->setDefaultSort(['name' => 'ASC'])
            ->setPaginatorUseOutputWalkers(false);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Ajouter une campagne')->setIcon('fas fa-plus')->setCssClass('btn btn-primary');
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
                return $action->setLabel('Créer la campagne')->setCssClass('btn btn-primary');
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
            TextField::new('name', 'Nom de la campagne')->setRequired(true),
            ChoiceField::new('type', 'Type')
                ->setChoices([
                    'Email'           => 'Email',
                    'Réseaux sociaux' => 'Réseaux sociaux',
                    'Google Ads'      => 'Google Ads',
                    'Événement'       => 'Événement',
                    'Autre'           => 'Autre',
                ])
                ->setRequired(true),
            DateField::new('startDate', 'Date de début')->setRequired(false),
            DateField::new('endDate', 'Date de fin')->setRequired(false),
            MoneyField::new('budget', 'Budget')
                ->setCurrency('MAD')
                ->setStoredAsCents(false)
                ->setRequired(false),
            TextField::new('utmSource', 'UTM Source')->setRequired(false),
            TextField::new('utmMedium', 'UTM Medium')->setRequired(false),
            TextField::new('utmCampaign', 'UTM Campaign')->setRequired(false),
            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'Active'   => 'Active',
                    'Terminée' => 'Terminée',
                ])
                ->setRequired(true),
            AssociationField::new('entity', 'Entité')
                ->setRequired(true)
                ->setFormTypeOptions(['choice_label' => 'name']),
            AssociationField::new('project', 'Projet associé')
                ->setRequired(false)
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