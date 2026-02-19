<?php

namespace App\Controller\Admin;

use App\Entity\Interaction;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class InteractionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Interaction::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Interaction')
            ->setEntityLabelInPlural('Interactions')
            ->setPageTitle('index', 'Historique des interactions')
            ->setPageTitle('new', 'Ajouter une interaction')
            ->setPageTitle('edit', 'Modifier l\'interaction')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            ChoiceField::new('type', 'Type')
                ->setChoices([
                    'Appel' => 'Appel',
                    'Email' => 'Email',
                    'Réunion' => 'Réunion',
                    'WhatsApp' => 'WhatsApp',
                    'Note' => 'Note',
                ])
                ->setRequired(true),
            TextField::new('subject', 'Sujet')
                ->setRequired(false),
            TextareaField::new('content', 'Contenu')
                ->setRequired(false),
            ChoiceField::new('outcome', 'Résultat')
                ->setChoices([
                    'Positif' => 'positif',
                    'Négatif' => 'négatif',
                    'Neutre' => 'neutre',
                ])
                ->setRequired(false),
            AssociationField::new('lead', 'Lead concerné')
                ->setRequired(true)
                ->setFormTypeOptions([
                    'choice_label' => 'fullName',
                ]),
            AssociationField::new('user', 'Auteur')
                ->setRequired(true)
                ->setFormTypeOptions([
                    'choice_label' => 'fullName',
                ]),
            DateTimeField::new('createdAt', 'Date')
                ->hideOnForm(),
        ];
    }
}