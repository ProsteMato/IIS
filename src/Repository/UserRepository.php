<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    /**
     * UserRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     *
     * Used to upgrade (rehash) the user's password automatically over time.
     * @param UserInterface $user user whose password will be changed
     * @param string $newEncodedPassword new password
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }


    /**
     * Fetches User from database whose email is param email
     *
     * @param string $email
     * @return int|mixed|string
     */
    public function findByEmail(string $email)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :val')
            ->setParameter('val', $email)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * Fetches visible Users from database
     *
     * @param int $num max number of required Users
     * @return int|mixed|string array of User object
     */
    public function getNumOpen(int $num)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.visibility = 1')
            ->orderBy('u.id', 'DESC')
            ->setMaxResults($num)
            ->getQuery()
            ->getResult()
            ;
    }
}
