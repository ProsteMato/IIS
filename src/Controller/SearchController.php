<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\User;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class SearchController
 *
 * Class handles search
 *
 * @author Magdaléna Ondrušková <xondru16@stud.fit.vutbr.cz>
 * @package App\Controller
 */
class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search", methods={"GET", "POST"})
     *
     * Main function for displaying searched objects
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserInterface|null $loggedUser user object, if not logged it is set to null
     * @return Response
     */
    public function search(Request $request,  EntityManagerInterface $entityManager, GroupRepository $groupRepo,
                           UserRepository $userRepo, UserInterface $loggedUser = null){
        $search_val = $request->query->get('search_val');
        $users = $this->search_users($entityManager, $search_val, $userRepo);
        $groups = $this->search_groups($entityManager, $search_val, $groupRepo);
        $count_users = count($users);
        $count_groups = count($groups);

        return $this->render('common/search.html.twig', ['search_val' => $search_val,
            'users' => $users,
            'users_count' => $count_users,
            'groups' => $groups,
            'groups_count' => $count_groups,
            'loggedUser'=> $loggedUser,
        ]);
    }


    /**
     * Function search for users in database - their first names or last names
     *
     * @param EntityManagerInterface $entityManager
     * @param $search_val 'searched value'
     * @return array Array of User-type objects
     */
    public function search_users(EntityManagerInterface $entityManager, $search_val, UserRepository $userRepo){

        if ($search_val )
        {
            $usersFirstName = $userRepo->findByFirstNameSubstr($search_val);
            $usersLastName = $userRepo->findByLastNameSubstr($search_val);
            $users = array_unique(array_merge($usersFirstName,$usersLastName), SORT_REGULAR);
        }
        else {
            $users = $entityManager->getRepository(User::class)->findAll();
        }

        return $users;
    }

    /**
     * Function search for groups in database - their names
     *
     * @param EntityManagerInterface $entityManager
     * @param $search_val 'searched value'
     * @return object[] object of groups
     */
    public function search_groups(EntityManagerInterface $entityManager, $search_val, GroupRepository $groupRepo){
        if ($search_val)
        {
            $groups = $groupRepo->findByNameSubstr($search_val);
        }
        else {
            $groups= $entityManager->getRepository(Group::class)->findAll();
        }


        return $groups;
    }
}
