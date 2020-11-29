<?php

namespace App\Repository;

use App\Entity\Group;
use App\Entity\GroupUser;
use App\Entity\Thread;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Thread|null find($id, $lockMode = null, $lockVersion = null)
 * @method Thread|null findOneBy(array $criteria, array $orderBy = null)
 * @method Thread[]    findAll()
 * @method Thread[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThreadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Thread::class);
    }


    /**
     * @return Thread[] Returns an array of Thread objects
     */
    public function getOpen(int $num)
    {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.group_id', 'g', 'WITH', 'g.visibility = 1')
            ->orderBy('t.id', 'DESC')
            ->setMaxResults($num)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Thread[] Returns an array of Thread objects
     */
    public function getTopOpen(int $num)
    {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.group_id', 'g', 'WITH', 'g.visibility = 1')
            ->orderBy('t.rating', 'DESC')
            ->setMaxResults($num)
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Thread[] Returns an array of Thread objects
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
    public function findOneBySomeField($value): ?Thread
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
