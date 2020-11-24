<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/users", name="show_list_users")
     */
    public function show(UserInterface $loggedUser): Response
    {

        $users =  $this->getDoctrine()->getRepository(User::class)->findAll();


        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'loggedUser' =>  $loggedUser,
            'users' => $users
        ]);
    }

    /**
     * @Route("/admin/delete/{id}", name="admin_delete")
     * @param UserInterface $loggedUser
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param int $id
     * @return void
     */
    public function delete_user(UserInterface $loggedUser, Request $request, EntityManagerInterface $entityManager, int $id){

        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        print($user->getId());

        // TODO kontrola ci user nahodou nema admina prideleneho - zo stranky sa sem nedostane ale moze to vytukat

        $entityManager->remove($user);
        $entityManager->flush();

        $response = new Response();
        $response->send();

        return $this->redirectToRoute('show_list_users');

    }
}