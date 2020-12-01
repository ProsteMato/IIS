<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\EditUserType;
use App\Form\Model\ChangePassword;
use App\Form\UserProfileType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use function Sodium\add;

/**
 * Class UserController
 *
 * Class that handles user profile and its editation
 *
 * @author Magdaléna Ondrušková <xondru16@stud.fit.vutbr.cz>
 * @package App\Controller
 */
class UserController extends AbstractController
{

    /**
     * User doesnt exist controller
     *
     * @Route("/no_user", name = "not_exist_user")
     * @param UserInterface|null $loggedUser
     * @return Response view access denied
     */
    public function no_user(UserInterface $loggedUser = null){
        return $this->render('common/user_doestn_exist.html.twig', [
            'loggedUser' => $loggedUser
        ]);
    }

    /**
     * @Route("/user/delete", name="delete_user")
     *
     * Function handles deleting logged-in users profile
     *
     * @param UserInterface $user
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    public function delete_user(Request $request, EntityManagerInterface $entityManager, UserInterface $user = null){

        //dump($user);

        //$this->delete_user_posts($user, $entityManager);

        $this->get('security.token_storage')->setToken(null); // odhlasenie uzivatela

        $entityManager->remove($user);
        $entityManager->flush();

        $response = new Response();
        $response->send();

        return $this->redirect('/');
    }
    /**
     * @Route("/user/{id}", name="user")
     *
     * Function shows user profile
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param int $id id of showed user
     * @param UserInterface $loggedUser looged in user - or null (if person is not logged in)
     * @return Response
     */
    public function index(Request $request, EntityManagerInterface $entityManager, int $id, UserInterface $loggedUser = null): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        if ($user){
            $groups = $user->getGroups();
            $posts = $user->getPosts();
        }
        else {
            return $this->redirectToRoute('not_exist_user');
        }


        $commonGroup = false;
        if ($loggedUser) {
            $loggedUser_groups = $loggedUser->getGroups();


            for ($i=0;  $i < count($groups); $i++){
                for ($j = 0; $j < count($loggedUser_groups); $j++) {
                    if ($groups[$i] == $loggedUser_groups[$j]){
                        $commonGroup = true;
                    }
                }
            }
        }

        return $this->render('user/viewprofile.html.twig', [
            'showedUser' => $user,
            'loggedUser' => $loggedUser,
            'id' => $id,
            'groups' => $groups,
            'posts' => $posts,
            'groups_count' => count($groups),
            'threads_count' =>count($user->getThreads()),
            'posts_count' => count($user->getPosts()),
            'common_group' => $commonGroup,
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/edit", name="edit_user", methods={"GET", "POST"})
     *
     * Function handles editation of  logged in user profile
     *
     * @param UserInterface $user
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function edit(Request $request, EntityManagerInterface $entityManager,
                         UserPasswordEncoderInterface $passwordEncoder, UserInterface $user = null){

        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            /* @var UploadedFile $file */
            $file = $form['attachment']->getData();
            print($file);
            if ($file) {
                $filename = md5(uniqid()) . '.' . $file->guessClientExtension();

                $file->move($this->getParameter('profilepics_dir'), $filename);
                $user->setProfilePicture($filename);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user', ['id' => $user->getId()]);
        }

        $changePasswordModel = new ChangePassword();
        $changePasswordForm = $this->createForm(ChangePasswordType::class, $changePasswordModel);
        $changePasswordForm->handleRequest($request);

        if ($changePasswordForm->isSubmitted() && $changePasswordForm->isValid()) {

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $changePasswordForm->get('newPassword')->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('edit_user');
        }


        return $this->render('user/edit.html.twig', array('user' => $user,
            'form' => $form->createView(),
            'changePasswordForm' => $changePasswordForm->createView(),
        ));
    }



    public function delete_user_posts(User $user, EntityManager $entityManager){
        $posts = $user->getPosts();
        foreach ($posts as $post){
            $post->setId(0);
            $entityManager->persist($post);
        }
        $entityManager->flush();
    }


    /**
     * @Route("/delete_photo", name="delete_photo")
     *
     * Function handles deleting current profile photo of user
     *
     * @param UserInterface $user
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    public function delete_photo(EntityManagerInterface $entityManager, UserInterface $user =null)
    {

        $user ->setProfilePicture('blank.png');

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('edit_user');
    }

    /**
     * @Route("/user{user_id}/follow", name="follow_user")
     *
     * Function handles adding a follower to user
     *
     * @param string $user_id
     * @param EntityManagerInterface $entityManager
     * @param UserInterface $user
     * @return Response
     */
    public function follow_user($user_id, EntityManagerInterface $entityManager, UserRepository $userRepository,
                                UserInterface $loggedUser =null)
    {
        $user = $userRepository->find($user_id);
        $loggedUser->addSubscriber($user);

        $entityManager->persist($loggedUser);
        $entityManager->flush();

        $groups = $loggedUser->getGroups();
        $posts = $loggedUser->getPosts();


        return $this->render('user/viewprofile.html.twig', [
            'showedUser' => $user,
            'loggedUser' => $loggedUser,
            'id' => $user_id,
            'groups' => $groups,
            'posts' => $posts,
            'groups_count' => count($groups),
            'threads_count' =>count($loggedUser->getThreads()),
            'posts_count' => count($loggedUser->getPosts()),
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/user{user_id}/unfollow", name="unfollow_user")
     *
     * Function hadnles removing follower from user
     *
     * @param string $user_id
     * @param EntityManagerInterface $entityManager
     * @param UserInterface $user
     * @return Response
     */
    public function unfollow_user($user_id, EntityManagerInterface $entityManager, UserRepository $userRepository,
                                UserInterface $loggedUser =null)
    {
        $user = $userRepository->find($user_id);
        $loggedUser->removeSubscriber($user);

        $entityManager->persist($loggedUser);
        $entityManager->flush();

        $groups = $loggedUser->getGroups();
        $posts = $loggedUser->getPosts();


        return $this->render('user/viewprofile.html.twig', [
            'showedUser' => $user,
            'loggedUser' => $loggedUser,
            'id' => $user_id,
            'groups' => $groups,
            'posts' => $posts,
            'groups_count' => count($groups),
            'threads_count' =>count($loggedUser->getThreads()),
            'posts_count' => count($loggedUser->getPosts()),
            'controller_name' => 'UserController',
        ]);
    }


}
