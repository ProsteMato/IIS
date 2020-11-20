<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LoggedUserController
 * @package App\Controller
 *
 * Class handles MainPage for logged in user.
 */
class LoggedUserController extends AbstractController
{
    /**
     * @Route("/home", name = "main_page_logged", methods={"GET"})
     */
    public function index()
    {
        // TODO: zobrazenie recently updated groups a najnovsie prispevky od users

        return $this->render('logged_user/index.html.twig');
    }
}
