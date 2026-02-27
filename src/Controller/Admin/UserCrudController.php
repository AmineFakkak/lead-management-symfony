<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->overrideTemplate('layout', 'admin/layout.html.twig')
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
            ->setPageTitle('index', 'Liste des utilisateurs')
            ->setPageTitle('new', 'Créer un utilisateur')
            ->setPageTitle('edit', 'Modifier l\'utilisateur')
            ->setDefaultSort(['fullName' => 'ASC'])
            ->setPaginatorUseOutputWalkers(false);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Ajouter un utilisateur')->setIcon('fas fa-plus')->setCssClass('btn btn-primary');
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
                return $action->setLabel('Créer l\'utilisateur')->setCssClass('btn btn-primary');
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
            TextField::new('fullName', 'Nom complet'),
            EmailField::new('email', 'Email'),
            TextField::new('password', 'Mot de passe')
                ->onlyOnForms()
                ->setRequired($pageName === Crud::PAGE_NEW),
            BooleanField::new('isActive', 'Actif'),
            AssociationField::new('entity', 'Entité'),
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