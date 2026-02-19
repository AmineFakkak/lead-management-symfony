<?php

namespace App\Controller\Admin;

use App\Entity\Task;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class TaskCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Task::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Tâche')
            ->setEntityLabelInPlural('Tâches')
            ->setPageTitle('index', 'Liste des tâches')
            ->setPageTitle('new', 'Créer une tâche')
            ->setPageTitle('edit', 'Modifier la tâche')
            ->setDefaultSort(['dueDate' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            
            TextField::new('title', 'Titre')
                ->setRequired(true),
            
            TextareaField::new('description', 'Description')
                ->hideOnIndex(),
            
            DateTimeField::new('dueDate', 'Échéance')
                ->setRequired(false)
                ->setFormat('dd/MM/yyyy HH:mm'), // Format français
            
            ChoiceField::new('priority', 'Priorité')
                ->setChoices([
                    'Basse' => 'Basse',
                    'Normale' => 'Normale',
                    'Haute' => 'Haute',
                    'Urgente' => 'Urgente',
                ])
                ->setRequired(true),
            
            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'À faire' => 'À faire',
                    'En cours' => 'En cours',
                    'Terminée' => 'Terminée',
                    'Annulée' => 'Annulée',
                ])
                ->setRequired(true),
            
            AssociationField::new('lead', 'Lead associé')
                ->setRequired(false)
                ->setFormTypeOptions([
                    'choice_label' => 'fullName',
                ]),
            
            AssociationField::new('assignedTo', 'Assigné à')
                ->setRequired(false)
                ->setFormTypeOptions([
                    'choice_label' => 'fullName',
                ]),
            
            // Dates automatiques
            DateTimeField::new('createdAt', 'Créé le')
                ->hideOnForm() // ← CACHÉ DANS LE FORMULAIRE
                ->setFormat('dd/MM/yyyy HH:mm'),
            
            DateTimeField::new('completedAt', 'Terminée le')
                ->hideOnForm() // ← CACHÉ DANS LE FORMULAIRE
                ->setFormat('dd/MM/yyyy HH:mm'),
        ];
    }
}