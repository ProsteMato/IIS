<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search", methods={"GET", "POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param $search_val
     * @return Response
     */
    public function search(Request $request, EntityManagerInterface $entityManager){
        $search_val = $request->query->get('search_val');
        $users = $this->search_users($entityManager, $search_val);
        $groups = $this->search_groups($entityManager, $search_val);
        $count_users = count($users);
        $count_groups = count($groups);
        return $this->render('common/search.html.twig', ['search_val' => $search_val,
            'users' => $users,
            'users_count' => $count_users,
            'groups' => $groups,
            'groups_count' => $count_groups,
            'loggedUser'=> null,
        ]);
    }



    public function search_users(EntityManagerInterface $entityManager, $search_val){
        //$search_val = $request->query->get('search_val');
        $usersFirstName = $entityManager->getRepository(User::class)->findBy([
            'firstName'  => $search_val]);
        $usersLastName = $entityManager->getRepository(User::class)->findBy([
            'lastName' => $search_val]);

        $users = array_merge($usersFirstName, $usersLastName);

        return $users;
    }

    public function search_groups(EntityManagerInterface $entityManager, $search_val){
        $groups = $entityManager->getRepository(Group::class)->findBy([
            'name'  => $search_val]);

        return $groups;
    }
}
