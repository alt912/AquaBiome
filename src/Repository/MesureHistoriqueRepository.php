<?php

namespace App\Repository;

use App\Entity\MesureHistorique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MesureHistorique>
 *
 * @method MesureHistorique|null find($id, $lockMode = null, $lockVersion = null)
 * @method MesureHistorique|null findOneBy(array $criteria, array $orderBy = null)
 * @method MesureHistorique[]    findAll()
 * @method MesureHistorique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MesureHistoriqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MesureHistorique::class);
    }
}
