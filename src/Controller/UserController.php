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
     * @Route("/user", name="user")
     * @param UserInterface $user
     * @param Request $request
     * @return Response
     */
    public function index(UserInterface $user, Request $request): Response
    {
        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);


        return $this->render('user/viewprofile.html.twig', [
            'user' => $user,
            'controller_name' => 'UserController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/edit", name="edit_user", methods={"GET", "POST"})
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

            $entityManager->flush();

            return $this->redirectToRoute('user');
        }
/**
        $file = [];
        $uploadFileForm = $this->createFormBuilder(UploadedFile::class, $file)
            ->add('attachment', FileType::class, [ 'mapped' => false,
            'required' => false,
            'label' => 'Upload profile photo'])
            ->getForm();

        $uploadFileForm->handleRequest($request);
*/
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

            return $this->redirectToRoute('user');
        }

        return $this->render('user/edit.html.twig', array('user' => $user,
            'form' => $form->createView(),
            'changePasswordForm' => $changePasswordForm->createView()));
    }

    /**
     * @Route("/user/delete", name="delete_user")
     * @param UserInterface $user
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    public function delete_user(UserInterface $user, Request $request, EntityManagerInterface $entityManager){
        $userId = $user->getId();

        $this->get('security.token_storage')->setToken(null); // odhlasenie uzivatela

        $entityManager->remove($user);
        $entityManager->flush();

        $response = new Response();
        $response->send();
        //return $this->render('unlogged/index.html.twig');
        return $this->redirectToRoute('main_page_unlogged');
    }




}
