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

/**
 * Class AdminController
 *
 * Class handles functions for admin user in our information system
 *
 * @author Magdaléna Ondrušková <xondru16@stud.fit.vutbr.cz>
 * @package App\Controller
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/admin/users", name="show_list_users")
     *
     * Function shows list of all users registered
     * @param UserInterface $loggedUser looged in user object
     * @return Response view
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
     *
     * Function shows list of all groups created
     * @param UserInterface $loggedUser looged in user object
     * @return Response view
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
     *
     * Function deletes user from system.
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

        $this->addFlash('notice', 'User was deleted');

        return $this->redirectToRoute('show_list_users');
    }
}
