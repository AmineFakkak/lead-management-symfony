<?php

namespace App\Controller\Admin;

use App\Entity\Project;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class ProjectCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Project::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Projet')
            ->setEntityLabelInPlural('Projets')
            ->setPageTitle('index', 'Liste des projets')
            ->setPageTitle('new', 'Créer un projet')
            ->setPageTitle('edit', 'Modifier le projet')
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Nom du projet')
                ->setRequired(true),
            TextareaField::new('description', 'Description')
                ->hideOnIndex(),
            DateField::new('startDate', 'Date de début')
                ->setRequired(false),
            DateField::new('endDate', 'Date de fin')
                ->setRequired(false),
            MoneyField::new('budget', 'Budget')
                ->setCurrency('MAD')
                ->setStoredAsCents(false)
                ->setRequired(false),
            IntegerField::new('targetLeads', 'Objectif leads')
                ->setRequired(false),
            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'En cours' => 'En cours',
                    'Terminé' => 'Terminé',
                    'Annulé' => 'Annulé',
                ])
                ->setRequired(true),
            AssociationField::new('entity', 'Entité')
                ->setRequired(true)
                ->setFormTypeOptions([
                    'choice_label' => 'name',
                ]),
            DateTimeField::new('createdAt', 'Créé le')
                ->hideOnForm(),
        ];
    }
}                                                                                                                                                                   