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
    /**
     * ThreadUserRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ThreadUser::class);
    }
}
