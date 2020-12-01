<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 *
 * Class that handles login and loging out
 *
 * @author Magdaléna Ondrušková <xondru16@stud.fit.vutbr.cz>
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     *
     * Function creates login formular and logs in user
     *
     * @param AuthenticationUtils $authenticationUtils
     * @param UserInterface|null $loggedUser logged in user - can be null
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils, UserInterface $loggedUser = null): Response
    {

        $user = new User();


        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();


        return $this->render('security/login.html.twig', ['last_username' => $lastUsername,
                                                                'loggedUser' => $loggedUser,
                                                                'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     *
     * Function handles logout for user
     */
    public function logout()
    {
        return $this->redirect('/');
    }
}
