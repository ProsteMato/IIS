<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
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
    public function show_users(UserInterface $loggedUser=null): Response
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
    public function show_groups(UserInterface $loggedUser = null): Response
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
    public function delete_user(Request $request, EntityManagerInterface $entityManager, int $id,
                                UserInterface $loggedUser = null){

        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $this->delete_user_actions($user, $entityManager);

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('show_list_users');
    }

    public function delete_user_actions(User $user, EntityManager $entityManager){
        $groups = $user->getGroups();

        foreach ($groups as $group){
            if ($group->getAdminUser() == $user){ // je vlastnikom
                $groupUser = $group->getGroupUser();
                foreach ($groupUser as &$gu){
                    $entityManager->remove($gu);
                }
                $threads = $group->getThreads();
                foreach ($threads as $thread){
                    foreach ($thread->getPosts() as $post){
                        $entityManager->remove($post);
                    }
                    foreach ($thread->getPostUsers() as $pu){
                        $entityManager->remove($pu);
                    }
                    foreach ($thread->getThreadUsers() as $tu){
                        $entityManager->remove($tu);
                    }
                    $entityManager->remove($thread);
                }
                $entityManager->remove($group);
            }
            else{ // je iba clenom - jeho prispevky sa nastavia na deleted user
                //nastavi userove posty autora na null
                $posts = $user->getPosts();
                foreach ($posts as $post){
                    $user->removePost($post);
                    $entityManager->persist($post);
                    $entityManager->persist($user);
                    $entityManager->flush();

                    foreach ($post->getPostUsers() as $post_likes) {
                        $user->removePostUser($post_likes);
                        $entityManager->persist($post_likes);
                        $entityManager->persist($user);
                        $entityManager->flush();
                    }

                }

                //nastavi userove thready autora na null
                $threads = $user->getThreads();
                foreach ($threads as $thread){
                    $user->removeThread($thread);
                    $entityManager->persist($thread);
                    $entityManager->persist($user);
                    $entityManager->flush();
                    foreach ($thread->getThreadUsers() as $thread_likes) {
                        $user->removeThreadUser($thread_likes);
                        $entityManager->persist($thread_likes);
                        $entityManager->persist($user);
                        $entityManager->flush();
                    }
                }
                // leavne groupy kde je clenom
                foreach ($user->getGroups() as $group_mem){
                    if(!$this->isGranted('GROUP_MEMBER', [$group_mem, $user])){
                        throw $this->createAccessDeniedException('not allowed');
                    }

                    $groupUser = $group_mem->getGroupUser();
                    foreach ($groupUser as &$gu){
                        if ($gu->getUser() == $user){
                            $entityManager->remove($gu);
                            $entityManager->flush();
                            break;
                        }
                    }
                }
            }

        }
        $entityManager->flush();
    }
}
