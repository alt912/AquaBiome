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
     * Finds relevant alerts that haven't been dismissed (vue = false).
     * @return Alerte[]
     */
    public function findRelevantAlerts(?int $latestMesureId): array
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.mesures', 'm')
            ->andWhere('a.vue = :notVue')
            ->setParameter('notVue', false)
            ->andWhere('m.id IS NULL'); // alertes manuelles (sans mesure liée)

        if ($latestMesureId) {
            $qb->orWhere('m.id = :latestId AND a.vue = false')
                ->setParameter('latestId', $latestMesureId);
        }

        return $qb->orderBy('a.dateAlerte', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Mark an alert as dismissed.
     */
    public function dismissAlert(int $id): void
    {
        $this->createQueryBuilder('a')
            ->update()
            ->set('a.vue', ':true')
            ->setParameter('true', true)
            ->where('a.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }
}
