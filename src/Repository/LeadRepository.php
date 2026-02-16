<?php

namespace App\Repository;

use App\Entity\Lead;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LeadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lead::class);
    }

    /**
     * Compte les leads par statut
     */
    public function countByStatut(): array
    {
        return $this->createQueryBuilder('l')
            ->select('l.status, COUNT(l.id) as nombre')
            ->groupBy('l.status')
            ->orderBy('nombre', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte les leads par entité
     */
    public function countByEntite(): array
    {
        return $this->createQueryBuilder('l')
->select('e.name as entite, COUNT(l.id) as nombre')            ->join('l.entity', 'e')
            ->groupBy('e.id')
            ->orderBy('nombre', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function countByStatus(array $statuses): int
{
    return $this->createQueryBuilder('l')
        ->select('COUNT(l.id)')
        ->where('l.status IN (:statuses)')
        ->setParameter('statuses', $statuses)
        ->getQuery()
        ->getSingleScalarResult();
}

public function countByMonth(): array
{
    $conn = $this->getEntityManager()->getConnection();
    $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as mois, COUNT(id) as nombre 
            FROM `lead` 
            GROUP BY mois 
            ORDER BY mois ASC";
    
    $stmt = $conn->executeQuery($sql);
    return $stmt->fetchAllAssociative();
}

public function countBySource(int $limit = 5): array
{
    return $this->createQueryBuilder('l')
        ->select('l.source, COUNT(l.id) as nombre')
        ->where('l.source IS NOT NULL')
        ->groupBy('l.source')
        ->orderBy('nombre', 'DESC')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
}
}