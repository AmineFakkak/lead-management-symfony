<?php

namespace App\DataFixtures;

use App\Entity\Entity;
use App\Entity\Lead;
use App\Entity\Project;
use App\Entity\Campaign;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $sources = Lead::SOURCES;
        $statuses = Lead::STATUSES;

        /*
        |---------------------------------------------------------
        | 1️⃣ Create Entity
        |---------------------------------------------------------
        */
        $entityFlash = new Entity();
        $entityFlash->setName('Flash Web')
            ->setSlug('flash-web')
            ->setColor('#3498db')
            ->setIsActive(true)
            ->setCreatedAt(new \DateTimeImmutable());

        $manager->persist($entityFlash);

        /*
        |---------------------------------------------------------
        | 2️⃣ Create Commercial User
        |---------------------------------------------------------
        */
        $commercial = new User();
        $commercial->setEmail('com@flash.ma')
            ->setFullName('Commercial Test')
            ->setRoles(['ROLE_COMMERCIAL'])
            ->setIsActive(true)
            ->setEntity($entityFlash);

        $hashedPassword = $this->passwordHasher->hashPassword(
            $commercial,
            'password123'
        );

        $commercial->setPassword($hashedPassword);

        $manager->persist($commercial);

        /*
        |---------------------------------------------------------
        | 3️⃣ Create Project
        |---------------------------------------------------------
        */
        $project = new Project();
        $project->setName('Site E-commerce')
            ->setStatus('En cours')
            ->setEntity($entityFlash)
            ->setCreatedAt(new \DateTimeImmutable());

        $manager->persist($project);

        /*
        |---------------------------------------------------------
        | 4️⃣ Create Campaign
        |---------------------------------------------------------
        */
        $campaign = new Campaign();
        $campaign->setName('Campagne Facebook')
            ->setType('Réseaux sociaux')
            ->setStatus('Active')
            ->setEntity($entityFlash)
            ->setProject($project)
            ->setCreatedAt(new \DateTimeImmutable());

        $manager->persist($campaign);

        /*
        |---------------------------------------------------------
        | 5️⃣ Create Leads
        |---------------------------------------------------------
        */
        for ($i = 0; $i < 10; $i++) {

            $lead = new Lead();
            $lead->setFullName($faker->name())
                ->setEmail($faker->email())
                ->setPhone($faker->phoneNumber())
                ->setCountry('Maroc')
                ->setSource($faker->randomElement($sources))
                ->setStatus($faker->randomElement($statuses))
                ->setScore($faker->numberBetween(0, 100))
                ->setEntity($entityFlash)
                ->setProject($project)
                ->setCampaign($campaign)
                ->setAssignedTo($commercial)
                ->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($lead);
        }

        $manager->flush();
    }
}
