<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\GroupRepository;
use App\Repository\ThreadRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class MainController
 *
 * Class that handles main page for both logged in and not users
 *
 * @author Magdaléna Ondrušková <xondru16@stud.fit.vutbr.cz>
 * @package App\Controller
 */
class MainController extends AbstractController
{
    /**
     * Maximum number of threads that should be displayed on main page
     */
    const MAX_THREADS = 200;

    /**
     * Maximum number of users that should be displayed on main page
     */
    const MAX_USERS = 200;

    /**
     *
     * Main page controller
     *
     * @Route("/", name = "main_page", methods={"GET"})
     * @param UserInterface|null $loggedUser object of logged in user - if no one is logged it is null
     * @param UserRepository $userRepository
     * @param ThreadRepository $threadRepository
     * @return Response view
     */
    public function main_page(UserInterface $loggedUser = null,
                              UserRepository $userRepository, ThreadRepository $threadRepository){
        $filter = 'New';
        $time_filter = "All Time";
        $my_filter = "All";
        if ($this->isGranted('ROLE_USER')){

            $users = $loggedUser->getSubscribers();

            $threads = [];
            $groups = $loggedUser->getGroups();
            foreach($groups as &$group){
                $threads_temp = $group->getThreads();
                foreach ($threads_temp as &$thread){
                    array_push($threads, $thread);
                }
            }
            usort($threads, function ($a, $b) {
                return $b->getCreationDate() <=> $a->getCreationDate();
            });

            return $this->render('user/index.html.twig', [
                'loggedUser' => $loggedUser,
                'threads' => $threads,
                'users' => $users,
                'currentFilter' => $filter,
                'timeFilter' => $time_filter,
                'currentMyFilter' => $my_filter
            ]);
        } else {
            $threads = $threadRepository->getOpen(self::MAX_THREADS, $time_filter);
            $users = $userRepository->getNumOpen(self::MAX_USERS);
            return $this->render('unlogged/index.html.twig', [
                'loggedUser' => $loggedUser,
                'threads' => $threads,
                'users' => $users,
                'currentFilter' => $filter,
                'timeFilter' => $time_filter,
            ]);
        }
    }

    /**
     * Help page controller
     *
     * @Route("/help", name = "help")
     * @return Response
     */
    public function help(UserInterface $loggedUser = null){
        return $this->render('common/help.html.twig', [ 'loggedUser' => $loggedUser]);
    }

    /**
     *
     * Main page filters controler
     *
     * @Route("/filter/{filter}/{time_filter}/{my_filter}", name = "main_page_filter", methods={"GET"})
     * @param EntityManagerInterface $entityManager
     * @param UserInterface|null $loggedUser object of logged in user - if no one is logged it is null
     * @return Response view with filtered posts
     */
    public function filter_threads($filter, $time_filter, $my_filter, UserInterface $loggedUser = null,
                            UserRepository $userRepository, ThreadRepository $threadRepository){

        if ($this->isGranted('ROLE_USER')){

            $users = $loggedUser->getSubscribers();

            if ($my_filter == 'My groups'){
                $threads = $this->filterGroups(self::MAX_THREADS, $filter, $time_filter, $loggedUser);
            } else {
                $threads = $this->filterAll(self::MAX_THREADS, $filter, $time_filter, $threadRepository);
            }

            return $this->render('user/index.html.twig', [
                'loggedUser' => $loggedUser,
                'threads' => array_slice($threads, 0, self::MAX_THREADS),
                'users' => $users,
                'currentFilter' => $filter,
                'timeFilter' => $time_filter,
                'currentMyFilter' => $my_filter
            ]);
        } else {
            $users = $userRepository->getNumOpen(self::MAX_USERS);
            $threads = $this->filterAll(self::MAX_THREADS, $filter, $time_filter, $threadRepository);

            return $this->render('unlogged/index.html.twig', [
                'loggedUser' => $loggedUser,
                'threads' => $threads,
                'users' => $users,
                'currentFilter' => $filter,
                'timeFilter' => $time_filter
            ]);
        }
    }


    /**
     * Access denied controller
     *
     * @Route("/acces_denied", name = "acces_denied")
     * @param UserInterface|null $loggedUser
     * @return Response view access denied
     */
    public function access_denied(UserInterface $loggedUser = null){
        return $this->render('common/access_denied.html.twig', [
            'loggedUser' => $loggedUser
        ]);
    }


    /**
     * Checks whether the timestamp is within last day - 24hours
     *
     * @param int $timestamp time to check in seconds since epoch
     * @return bool
     */
    private function checkTimeToday(int $timestamp) : bool
    {
        return (time()-(24*60*60)) < $timestamp;
    }


    /**
     * Checks whether the timestamp is within last week - 7 days
     *
     * @param int $timestamp time to check in seconds since epoch
     * @return bool
     */
    private function checkTimeWeek(int $timestamp) : bool
    {
        return (time()-(7*24*60*60)) < $timestamp;
    }

    /**
     * Checks whether the timestamp is within last month - 30 days
     *
     * @param int $timestamp time to check in seconds since epoch
     * @return bool
     */
    private function checkTimeMonth(int $timestamp) : bool
    {
        return (time()-(30*24*60*60)) < $timestamp;
    }

    /**
     * Checks whether the timestamp is within last year
     *
     * @param int $timestamp time to check in seconds since epoch
     * @return bool
     */
    private function checkTimeYear(int $timestamp) : bool
    {
        return (time()-(365*24*60*60)) < $timestamp;
    }

    /**
     * Filters threads the are visible to all users
     *
     * @param int $limit max number of required threads
     * @param string $filter primary filter
     * @param string $time_filter time filter
     * @param ThreadRepository $threadRepository
     * @return \App\Entity\Thread[] filtered threads
     */
    private function filterAll(int $limit, string $filter, string $time_filter, ThreadRepository $threadRepository)
    {
        if ($filter == 'New'){
            $threads = $threadRepository->getOpen($limit, $time_filter);
        } elseif ($filter == 'Top'){
            $threads = $threadRepository->getTopOpen($limit, $time_filter);
        } elseif ($filter == 'Most viewed'){
            $threads = $threadRepository->getViewedOpen($limit, $time_filter);
        } elseif ($filter == "Most commented"){
            $threads = $threadRepository->getOpen($limit, $time_filter);
            usort($threads, function ($a, $b) {
                return $b->getPosts()->count() <=> $a->getPosts()->count();
            });
        }
        return $threads;
    }

    /**
     * Filters threads that are from the groups in which user is
     *
     * @param int $limit max number of required threads
     * @param string $filter primary filter
     * @param string $time_filter time filter
     * @param User $user logged user
     * @return \App\Entity\Thread[] filtered threads
     */
    private function filterGroups(int $limit, string $filter, string $time_filter, User $user)
    {
        $threads = [];
        // filtering by time
        $this->timeFilter($time_filter, $user, $threads);
        // primary filter
        $this->primaryFilter($filter,$threads);
        return array_slice($threads, 0, $limit);
    }

    /**
     * Filters threads according to time of their creation
     *
     * @param string $time_filter time filter
     * @param User $user logged user
     * @param Array $threads output input array of threads
     */
    private function timeFilter(string $time_filter, User $user, Array &$threads)
    {
        $groups = $user->getGroups();
        foreach($groups as &$group){
            $threads_temp = $group->getThreads();
            foreach ($threads_temp as &$thread){
                if ($time_filter == "Today"){
                    if ($this->checkTimeToday($thread->getCreationDate()->getTimestamp())){
                        array_push($threads, $thread);
                    }
                } elseif ($time_filter == "Week"){
                    if ($this->checkTimeWeek($thread->getCreationDate()->getTimestamp())){
                        array_push($threads, $thread);
                    }
                } elseif ($time_filter == "Month"){
                    if ($this->checkTimeMonth($thread->getCreationDate()->getTimestamp())){
                        array_push($threads, $thread);
                    }
                } elseif ($time_filter == "Year"){
                    if ($this->checkTimeYear($thread->getCreationDate()->getTimestamp())){
                        array_push($threads, $thread);
                    }
                } else {
                    $time_filter = "All Time";
                    array_push($threads, $thread);
                }
            }
        }
    }

    /**
     * Filters threads according to primary filter
     *
     * @param string $filter primary filter
     * @param Array $threads output input array of threads
     */
    private function primaryFilter(string $filter, Array &$threads)
    {
        if ($filter == 'New'){
            usort($threads, function ($a, $b) {
                return $b->getCreationDate() <=> $a->getCreationDate();
            });
        } elseif ($filter == 'Top'){
            usort($threads, function ($a, $b) {
                return $b->getRating() <=> $a->getRating();
            });
        } elseif ($filter == 'Most viewed'){
            usort($threads, function ($a, $b) {
                return $b->getViews() <=> $a->getViews();
            });
        } elseif ($filter == 'Most commented'){
            usort($threads, function ($a, $b) {
                return $b->getPosts()->count() <=> $a->getPosts()->count();
            });
        }
    }

}
