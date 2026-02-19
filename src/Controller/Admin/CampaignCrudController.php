<?php

namespace App\Controller\Admin;

use App\Entity\Campaign;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class CampaignCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Campaign::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Campagne')
            ->setEntityLabelInPlural('Campagnes')
            ->setPageTitle('index', 'Liste des campagnes')
            ->setPageTitle('new', 'Créer une campagne')
            ->setPageTitle('edit', 'Modifier la campagne')
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Nom de la campagne')
                ->setRequired(true),
            ChoiceField::new('type', 'Type')
                ->setChoices([
                    'Email' => 'Email',
                    'Réseaux sociaux' => 'Réseaux sociaux',
                    'Google Ads' => 'Google Ads',
                    'Événement' => 'Événement',
                ])
                ->setRequired(true),
            DateField::new('startDate', 'Date de début')
                ->setRequired(false),
            DateField::new('endDate', 'Date de fin')
                ->setRequired(false),
            MoneyField::new('budget', 'Budget')
                ->setCurrency('MAD')
                ->setStoredAsCents(false)
                ->setRequired(false),
            TextField::new('utmSource', 'UTM Source')
                ->setRequired(false),
            TextField::new('utmMedium', 'UTM Medium')
                ->setRequired(false),
            TextField::new('utmCampaign', 'UTM Campaign')
                ->setRequired(false),
            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'Active' => 'Active',
                    'Terminée' => 'Terminée',
                ])
                ->setRequired(true),
            AssociationField::new('entity', 'Entité')
                ->setRequired(true)
                ->setFormTypeOptions([
                    'choice_label' => 'name',
                ]),
            AssociationField::new('project', 'Projet associé')
                ->setRequired(false)
                ->setFormTypeOptions([
                    'choice_label' => 'name',
                ]),
            DateTimeField::new('createdAt', 'Créé le')
                ->hideOnForm(),
        ];
    }
}