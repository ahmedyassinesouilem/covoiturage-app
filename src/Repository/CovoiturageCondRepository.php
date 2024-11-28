<?php

namespace App\Repository;

use App\Entity\CovoiturageCond;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CovoiturageCond>
 */
class CovoiturageCondRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CovoiturageCond::class);
    }

    //    /**
    //     * @return CovoiturageCond[] Returns an array of CovoiturageCond objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CovoiturageCond
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
