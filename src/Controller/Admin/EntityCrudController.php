<?php

namespace App\Controller\Admin;

use App\Entity\Entity;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class EntityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Entity::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Entité')
            ->setEntityLabelInPlural('Entités')
            ->setPageTitle('index', 'Liste des entités')
            ->setPageTitle('new', 'Créer une entité')
            ->setPageTitle('edit', 'Modifier l\'entité')
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Nom')
                ->setRequired(true),
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
            BooleanField::new('isActive', 'Actif'),
            DateTimeField::new('createdAt', 'Créé le')
                ->hideOnForm(),
        ];
    }
}