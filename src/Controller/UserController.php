<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\EditUserType;
use App\Form\Model\ChangePassword;
use App\Form\UserProfileType;
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

class UserController extends AbstractController
{

    /**
     * @Route("/user/{id}", name="user")
     *
     * Function shows user profile
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param int $id
     * @param UserInterface $loggedUser
     * @return Response
     */
    public function index(Request $request, EntityManagerInterface $entityManager, int $id, UserInterface $loggedUser = null): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        return $this->render('user/viewprofile.html.twig', [
            'showedUser' => $user,
            'loggedUser' => $loggedUser,
            'id' => $id,
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/edit", name="edit_user", methods={"GET", "POST"})
     * @param UserInterface $user
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function edit(UserInterface $user,  Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {

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
            /**
             * TODO: finish this - vrati true ak sa hesla zhoduju

             if ($changePasswordForm->get('oldPassword')->getData() == $changePasswordForm->get('newPassword')->getData())
            {

            }
            */
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

    /**
     * @Route("/user/delete", name="delete_user")
     * @param UserInterface $user
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    public function delete_user(UserInterface $user, Request $request, EntityManagerInterface $entityManager){

        $this->get('security.token_storage')->setToken(null); // odhlasenie uzivatela

        $entityManager->remove($user);
        $entityManager->flush();

        $response = new Response();
        $response->send();

        return $this->redirectToRoute('main_page_unlogged');
    }


    /**
     * @Route("/delete_photo", name="delete_photo")
     * @param UserInterface $user
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    public function delete_photo(UserInterface $user, EntityManagerInterface $entityManager)
    {

        $user ->setProfilePicture('blank.png');

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('edit_user');
    }



}
