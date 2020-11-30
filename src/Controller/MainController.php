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
     * @Route("/", name = "main_page", methods={"GET"})
     * @param Request $request
     * @param UserInterface|null $loggedUser object of logged in user - if no one is logged it is null
     * @param UserRepository $userRepository
     * @param ThreadRepository $threadRepository
     * @return Response view
     */
    public function main_page(Request $request, UserInterface $loggedUser = null,
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
                'threads' => array_slice($threads, 0, 20),
                'users' => $users,
                'currentFilter' => $filter,
                'timeFilter' => $time_filter,
                'currentMyFilter' => $my_filter
            ]);
        } else {
            $threads = $threadRepository->getOpen(20, $time_filter);
            $users = $userRepository->getNumOpen(40);
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
     * @Route("/help", name = "help")
     * @return Response
     */
    public function help(UserInterface $loggedUser = null){
        return $this->render('common/help.html.twig', [ 'loggedUser' => $loggedUser]);
    }

    /**
     * @Route("/filter/{filter}/{time_filter}/{my_filter}", name = "main_page_filter", methods={"GET"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserInterface|null $loggedUser object of logged in user - if no one is logged it is null
     * @return Response view
     */
    public function filter_threads($filter, $time_filter, $my_filter, Request $request, UserInterface $loggedUser = null,
                            UserRepository $userRepository, ThreadRepository $threadRepository){

        if ($this->isGranted('ROLE_USER')){

            $users = $loggedUser->getSubscribers();

            if ($my_filter == 'My groups'){
                // filtering by time
                $threads = [];
                $groups = $loggedUser->getGroups();
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

                // primary filter
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
            } else {
                if ($filter == 'New'){
                    $threads = $threadRepository->getOpen(200, $time_filter);
                } elseif ($filter == 'Top'){
                    $threads = $threadRepository->getTopOpen(200, $time_filter);
                } elseif ($filter == 'Most viewed'){
                    $threads = $threadRepository->getViewedOpen(200, $time_filter);
                } elseif ($filter == "Most commented"){
                    $threads = $threadRepository->getOpen(200, $time_filter);
                    usort($threads, function ($a, $b) {
                        return $b->getPosts()->count() <=> $a->getPosts()->count();
                    });
                    $threads = array_slice($threads, 0, 200);
                }
            }



            return $this->render('user/index.html.twig', [
                'loggedUser' => $loggedUser,
                'threads' => array_slice($threads, 0, 20),
                'users' => $users,
                'currentFilter' => $filter,
                'timeFilter' => $time_filter,
                'currentMyFilter' => $my_filter
            ]);
        } else {
            $users = $userRepository->getNumOpen(40);

            if ($filter == 'New'){
                $threads = $threadRepository->getOpen(20, $time_filter);
            } elseif ($filter == 'Top'){
                $threads = $threadRepository->getTopOpen(20, $time_filter);
            } elseif ($filter == 'Most viewed'){
                $threads = $threadRepository->getViewedOpen(200, $time_filter);
            } elseif ($filter == "Most commented"){
                $threads = $threadRepository->getOpen(20, $time_filter);
                usort($threads, function ($a, $b) {
                    return $b->getPosts()->count() <=> $a->getPosts()->count();
                });
                $threads = array_slice($threads, 0, 20);
            }

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
     * @Route("/acces_denied", name = "acces_denied")
     * @param UserInterface|null $loggedUser
     * @return Response
     */
    public function access_denied(UserInterface $loggedUser = null){
        return $this->render('common/access_denied.html.twig', [
            'loggedUser' => $loggedUser
        ]);
    }


    public function checkTimeToday(int $timestamp) : bool
    {
        return (time()-(24*60*60)) < $timestamp;
    }

    public function checkTimeWeek(int $timestamp) : bool
    {
        return (time()-(7*24*60*60)) < $timestamp;
    }

    public function checkTimeMonth(int $timestamp) : bool
    {
        return (time()-(30*24*60*60)) < $timestamp;
    }

    public function checkTimeYear(int $timestamp) : bool
    {
        return (time()-(365*24*60*60)) < $timestamp;
    }
}
