<?php

namespace App\Controller\Admin;

use App\Entity\Interaction;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;

class InteractionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Interaction::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->overrideTemplate('layout', 'admin/layout.html.twig')
            ->setEntityLabelInSingular('Interaction')
            ->setEntityLabelInPlural('Interactions')
            ->setPageTitle('index', 'Historique des interactions')
            ->setPageTitle('new', 'Ajouter une interaction')
            ->setPageTitle('edit', 'Modifier l\'interaction')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPaginatorUseOutputWalkers(false);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Ajouter une interaction')->setIcon('fas fa-plus')->setCssClass('btn btn-primary');
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
                return $action->setLabel('Créer l\'interaction')->setCssClass('btn btn-primary');
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
            ChoiceField::new('type', 'Type')
                ->setChoices([
                    'Appel'    => 'Appel',
                    'Email'    => 'Email',
                    'Réunion'  => 'Réunion',
                    'WhatsApp' => 'WhatsApp',
                    'Note'     => 'Note',
                ])
                ->setRequired(true),
            TextField::new('subject', 'Sujet')->setRequired(false),
            TextareaField::new('content', 'Contenu')->setRequired(false),
            ChoiceField::new('outcome', 'Résultat')
                ->setChoices([
                    'Positif' => 'positif',
                    'Négatif' => 'négatif',
                    'Neutre'  => 'neutre',
                ])
                ->setRequired(false),
            AssociationField::new('lead', 'Lead concerné')
                ->setRequired(true)
                ->setFormTypeOptions(['choice_label' => 'fullName']),
            AssociationField::new('user', 'Auteur')
                ->setRequired(true)
                ->setFormTypeOptions(['choice_label' => 'fullName']),
            DateTimeField::new('createdAt', 'Date')->hideOnForm(),
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

        // Interactions on leads assigned to user
        $qb->innerJoin('entity.lead', 'l')
           ->andWhere('l.assignedTo = :user')
           ->setParameter('user', $user);

        return $qb;
    }
}