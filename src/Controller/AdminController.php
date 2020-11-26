<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Error\RuntimeError;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/users", name="show_list_users")
     */
    public function show_users(UserInterface $loggedUser): Response
    {

        $users =  $this->getDoctrine()->getRepository(User::class)->findAll();


        return $this->render('admin/users.html.twig', [
            'controller_name' => 'AdminController',
            'loggedUser' =>  $loggedUser,
            'users' => $users
        ]);
    }

    /**
     * @Route("/admin/groups", name="show_list_groups")
     */
    public function show_groups(UserInterface $loggedUser): Response
    {

        $groups =  $this->getDoctrine()->getRepository(Group::class)->findAll();

        return $this->render('admin/groups.html.twig', [
            'controller_name' => 'AdminController',
            'loggedUser' =>  $loggedUser,
            'groups' => $groups
        ]);
    }

    /**
     * @Route("/admin/delete/{id}", name="admin_delete")
     * @param UserInterface $loggedUser
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param int $id
     * @return RedirectResponse
     */
    public function delete_user(UserInterface $loggedUser, Request $request, EntityManagerInterface $entityManager, int $id){

        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        $entityManager->remove($user);
        $entityManager->flush();

        $response = new Response();
        $response->send();

        return $this->redirectToRoute('show_list_users');
    }
}
