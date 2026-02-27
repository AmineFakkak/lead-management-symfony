<?php

namespace App\Controller\Admin;

use App\Entity\Entity as EntityEntity;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;

class EntityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return EntityEntity::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->overrideTemplate('layout', 'admin/layout.html.twig')
            ->setEntityLabelInSingular('Entité')
            ->setEntityLabelInPlural('Entités')
            ->setPageTitle('index', 'Liste des entités')
            ->setPageTitle('new', 'Créer une entité')
            ->setPageTitle('edit', 'Modifier l\'entité')
            ->setDefaultSort(['name' => 'ASC'])
            ->setPaginatorUseOutputWalkers(false);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Ajouter une entité')->setIcon('fas fa-plus')->setCssClass('btn btn-primary');
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
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE]);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Nom')->setRequired(true),
            TextField::new('slug', 'Slug')
                ->setRequired(true)
                ->setHelp('Identifiant unique pour l\'URL'),
            ImageField::new('logo', 'Logo')
                ->setBasePath('uploads/logos')
                ->setUploadDir('public/uploads/logos')
                ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
                ->setRequired(false),
            ColorField::new('color', 'Couleur')
                ->setRequired(false)
                ->setHelp('Code hexadécimal (ex: #3498db)'),
            BooleanField::new('isActive', 'Actif')
                ->renderAsSwitch(),
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

        $qb->andWhere('entity.id = :entityId')
           ->setParameter('entityId', $entity->getId());

        return $qb;
    }
}