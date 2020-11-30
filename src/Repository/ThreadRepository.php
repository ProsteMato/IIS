<?php

namespace App\Repository;

use App\Entity\Group;
use App\Entity\GroupUser;
use App\Entity\Thread;
use App\Entity\User;
use DateTime;
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
    /**
     * ThreadRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Thread::class);
    }


    /**
     * Fetches Threads from database, that are visible and according to time filter, sorted by creation_date
     *
     * @param int $num max number of Threads required
     * @param string $timeFilter time filter
     * @return int|mixed|string queried Threads
     */
    public function getOpen(int $num, string $timeFilter)
    {
        $datetime = new DateTime();
        if ($timeFilter == "Today"){
            $datetime->setTimestamp(time()-(24*60*60));
        } elseif ($timeFilter == "Week"){
            $datetime->setTimestamp(time()-(7*24*60*60));
        } elseif ($timeFilter == "Month"){
            $datetime->setTimestamp(time()-(30*24*60*60));
        } elseif ($timeFilter == "Year"){
            $datetime->setTimestamp(time()-(365*24*60*60));
        } else {
            $datetime->setTimestamp(0);
        }

        return $this->createQueryBuilder('t')
            ->innerJoin('t.group_id', 'g', 'WITH', 'g.visibility = 1')
            ->andWhere('t.creation_date > :date')
            ->setParameter('date', $datetime)
            ->orderBy('t.creation_date', 'DESC')
            ->setMaxResults($num)
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * Fetches Threads from database, that are visible, according to time filter and sorted by rating
     *
     * @param int $num max number of Threads required
     * @param string $timeFilter time filter
     * @return int|mixed|string queried Threads
     */
    public function getTopOpen(int $num, string $timeFilter)
    {
        $datetime = new DateTime();
        if ($timeFilter == "Today"){
            $datetime->setTimestamp(time()-(24*60*60));
        } elseif ($timeFilter == "Week"){
            $datetime->setTimestamp(time()-(7*24*60*60));
        } elseif ($timeFilter == "Month"){
            $datetime->setTimestamp(time()-(30*24*60*60));
        } elseif ($timeFilter == "Year"){
            $datetime->setTimestamp(time()-(365*24*60*60));
        } else {
            $datetime->setTimestamp(0);
        }

        return $this->createQueryBuilder('t')
            ->innerJoin('t.group_id', 'g', 'WITH', 'g.visibility = 1')
            ->andWhere('t.creation_date > :date')
            ->setParameter('date', $datetime)
            ->orderBy('t.rating', 'DESC')
            ->setMaxResults($num)
            ->getQuery()
            ->getResult()
            ;
    }


    /**
     * Fetches Threads from database, that are visible, according to time filter and sorted by views
     *
     * @param int $num max number of Threads required
     * @param string $timeFilter time filter
     * @return int|mixed|string queried Threads
     */
    public function getViewedOpen(int $num, string $timeFilter)
    {
        $datetime = new DateTime();
        if ($timeFilter == "Today"){
            $datetime->setTimestamp(time()-(24*60*60));
        } elseif ($timeFilter == "Week"){
            $datetime->setTimestamp(time()-(7*24*60*60));
        } elseif ($timeFilter == "Month"){
            $datetime->setTimestamp(time()-(30*24*60*60));
        } elseif ($timeFilter == "Year"){
            $datetime->setTimestamp(time()-(365*24*60*60));
        } else {
            $datetime->setTimestamp(0);
        }

        return $this->createQueryBuilder('t')
            ->innerJoin('t.group_id', 'g', 'WITH', 'g.visibility = 1')
            ->andWhere('t.creation_date > :date')
            ->setParameter('date', $datetime)
            ->orderBy('t.views', 'DESC')
            ->setMaxResults($num)
            ->getQuery()
            ->getResult()
            ;
    }
}
