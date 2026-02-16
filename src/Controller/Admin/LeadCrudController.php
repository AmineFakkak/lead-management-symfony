<?php

namespace App\Controller\Admin;

use App\Entity\Lead;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class LeadCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Lead::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('fullName', 'Nom complet'),
            EmailField::new('email', 'Email'),
            TelephoneField::new('phone', 'Téléphone'),
            TelephoneField::new('whatsapp', 'WhatsApp')->hideOnIndex(),
            TextField::new('company', 'Entreprise')->hideOnIndex(),
            TextField::new('jobTitle', 'Fonction')->hideOnIndex(),
            TextField::new('city', 'Ville'),
            TextField::new('country', 'Pays'),
            
            // Champ source avec choix prédéfinis
            ChoiceField::new('source', 'Source')
                ->setChoices([
                    'Site web' => 'Website',
                    'Facebook' => 'Facebook',
                    'Google Ads' => 'Google Ads',
                    'Recommandation' => 'Recommandation',
                    'Événement' => 'Événement',
                    'WhatsApp' => 'WhatsApp',
                ]),
            
            // Champ statut avec choix prédéfinis
            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'Nouveau' => 'Nouveau',
                    'Contacté' => 'Contacté',
                    'Qualifié' => 'Qualifié',
                    'Proposition' => 'Proposition',
                    'Négociation' => 'Négociation',
                    'Gagné' => 'Gagné',
                    'Perdu' => 'Perdu',
                    'Reporté' => 'Reporté',
                ]),
            
            IntegerField::new('score', 'Score')->hideOnIndex(),
            TextareaField::new('notes', 'Notes')->hideOnIndex(),
            
            DateTimeField::new('createdAt', 'Créé le')->hideOnForm(),
            DateTimeField::new('updatedAt', 'Modifié le')->hideOnForm(),
            DateTimeField::new('convertedAt', 'Converti le')->hideOnForm(),
            
            // Relations
            AssociationField::new('entity', 'Entité')
                ->setRequired(true), // important : obligatoire
            AssociationField::new('project', 'Projet')
                ->setRequired(false),
            AssociationField::new('campaign', 'Campagne')
                ->setRequired(false),
            AssociationField::new('assignedTo', 'Assigné à')
                ->setRequired(false),
            AssociationField::new('tags', 'Tags')
                ->setRequired(false)
                ->hideOnIndex(),
        ];
    }
}