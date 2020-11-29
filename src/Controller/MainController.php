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
     * @Route("/{filter}", name = "main_page", methods={"GET"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserInterface|null $loggedUser object of logged in user - if no one is logged it is null
     * @return Response view
     */
    public function main_page($filter = 'recent', Request $request, EntityManagerInterface $entityManager, UserInterface $loggedUser = null,
                              GroupRepository $groupRepository, UserRepository $userRepository, ThreadRepository $threadRepository){

        if ($this->isGranted('ROLE_USER')){

            $users = $loggedUser->getSubscribers();

            if ($filter == 'recent'){
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
            }

            return $this->render('user/index.html.twig', [
                'loggedUser' => $loggedUser,
                'threads' => array_slice($threads, 0, 20),
                'users' => $users,
                'currentFilter' => $filter
            ]);
        } else {
            $threads = $threadRepository->getNumOpen(20);
            $users = $userRepository->getNumOpen(40);
            return $this->render('unlogged/index.html.twig', [
                'loggedUser' => $loggedUser,
                'threads' => $threads,
                'users' => $users,
                'currentFilter' => $filter
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




}
