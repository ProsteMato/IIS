<?php

namespace App\Repository;

use App\Entity\ThreadUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ThreadUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method ThreadUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method ThreadUser[]    findAll()
 * @method ThreadUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThreadUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ThreadUser::class);
    }

    // /**
    //  * @return ThreadUser[] Returns an array of ThreadUser objects
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
    public function findOneBySomeField($value): ?ThreadUser
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
