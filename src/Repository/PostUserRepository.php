<?php

namespace App\Repository;

use App\Entity\PostUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PostUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostUser[]    findAll()
 * @method PostUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostUserRepository extends ServiceEntityRepository
{
    /**
     * PostUserRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostUser::class);
    }
}
