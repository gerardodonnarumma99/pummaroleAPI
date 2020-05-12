<?php

namespace App\Repository;

use App\Entity\TimerType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TimerType|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimerType|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimerType[]    findAll()
 * @method TimerType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimerTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimerType::class);
    }

    // /**
    //  * @return TimerType[] Returns an array of TimerType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TimerType
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
