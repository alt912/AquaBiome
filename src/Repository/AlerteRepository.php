<?php

namespace App\Repository;

use App\Entity\Alerte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Alerte>
 */
class AlerteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alerte::class);
    }

    //    /**
    //     * @return Alerte[] Returns an array of Alerte objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Alerte
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    /**
     * Finds manual alerts (no associated measure) AND the alert for the specific latest measure.
     * @return Alerte[]
     */
    public function findRelevantAlerts(?int $latestMesureId): array
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.mesures', 'm')
            ->where('m.id IS NULL'); // Manual alerts (no linked measure)

        if ($latestMesureId) {
            $qb->orWhere('m.id = :latestId')
                ->setParameter('latestId', $latestMesureId);
        }

        return $qb->orderBy('a.dateAlerte', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
