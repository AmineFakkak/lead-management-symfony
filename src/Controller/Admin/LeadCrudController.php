<?php

namespace App\Controller\Admin;

use App\Entity\Lead;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;

class LeadCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Lead::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->overrideTemplate('layout', 'admin/layout.html.twig')
            ->setEntityLabelInSingular('Lead')
            ->setEntityLabelInPlural('Leads')
            ->setPageTitle(Crud::PAGE_INDEX, 'Gestion des Leads')
            ->setPageTitle(Crud::PAGE_NEW, 'Créer un nouveau lead')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier le lead')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Détails du lead')
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(20)
            ->setSearchFields(['fullName', 'email', 'phone', 'company', 'city']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Ajouter un lead')->setIcon('fas fa-plus')->setCssClass('btn btn-primary');
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
                return $action->setLabel('Créer le lead')->setCssClass('btn btn-primary');
            })
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER, function (Action $action) {
                return $action->setLabel('Créer et ajouter un autre')->setCssClass('btn btn-secondary');
            })
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE]);
    }

    public function configureFields(string $pageName): iterable
    {
        $isIndex = $pageName === Crud::PAGE_INDEX;

        $statusBadges = [
            'Nouveau'     => 'primary',
            'Contacté'    => 'info',
            'Qualifié'    => 'warning',
            'Proposition' => 'warning',
            'Négociation' => 'secondary',
            'Gagné'       => 'success',
            'Perdu'       => 'danger',
            'Reporté'     => 'secondary',
        ];

        return [
            TextField::new('fullName', 'Nom complet')
                ->setHelp($isIndex ? '' : 'Nom et prénom du contact'),
            EmailField::new('email', 'Email')
                ->setHelp($isIndex ? '' : 'Adresse email professionnelle'),
            TelephoneField::new('phone', 'Téléphone')
                ->setHelp($isIndex ? '' : 'Numéro de téléphone'),
            TelephoneField::new('whatsapp', 'WhatsApp')->hideOnIndex(),
            TextField::new('company', 'Entreprise')->hideOnIndex(),
            TextField::new('jobTitle', 'Fonction')->hideOnIndex(),
            TextField::new('city', 'Ville')
                ->setHelp($isIndex ? '' : 'Ville du lead'),
            TextField::new('country', 'Pays')->hideOnIndex(),
            ChoiceField::new('source', 'Source')
                ->setChoices([
                    'Site web'       => 'Website',
                    'Facebook'       => 'Facebook',
                    'Google Ads'     => 'Google Ads',
                    'Recommandation' => 'Recommandation',
                    'Événement'      => 'Événement',
                    'WhatsApp'       => 'WhatsApp',
                ])
                ->setHelp($isIndex ? '' : "Source d'acquisition"),
            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'Nouveau'     => 'Nouveau',
                    'Contacté'    => 'Contacté',
                    'Qualifié'    => 'Qualifié',
                    'Proposition' => 'Proposition',
                    'Négociation' => 'Négociation',
                    'Gagné'       => 'Gagné',
                    'Perdu'       => 'Perdu',
                    'Reporté'     => 'Reporté',
                ])
                ->renderAsBadges($statusBadges)
                ->setHelp($isIndex ? '' : 'Statut actuel du lead'),
            IntegerField::new('score', 'Score')->hideOnIndex()->setHelp('Score de 1 à 100'),
            TextareaField::new('notes', 'Notes')->hideOnIndex(),
            DateTimeField::new('createdAt', 'Créé le')->hideOnForm()->setFormat('dd/MM/yyyy HH:mm'),
            DateTimeField::new('updatedAt', 'Modifié le')->hideOnForm()->hideOnIndex()->setFormat('dd/MM/yyyy HH:mm'),
            DateTimeField::new('convertedAt', 'Converti le')->hideOnForm()->hideOnIndex()->setFormat('dd/MM/yyyy HH:mm'),
            AssociationField::new('entity', 'Entité')->setRequired(true),
            AssociationField::new('project', 'Projet')->setRequired(false)->hideOnIndex(),
            AssociationField::new('campaign', 'Campagne')->setRequired(false)->hideOnIndex(),
            AssociationField::new('assignedTo', 'Assigné à')->setRequired(false),
            AssociationField::new('tags', 'Tags')
                ->setRequired(false)->hideOnIndex()
                ->setFormTypeOption('by_reference', false),
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

        // Commercial: only leads assigned to them
        $qb->andWhere('entity.assignedTo = :user')
           ->setParameter('user', $user);

        return $qb;
    }
}