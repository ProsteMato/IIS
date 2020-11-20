<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; // for routing -  changing the web address
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MainController
 * @package App\Controller
 *
 * Class handles main page (first page user (unlogged) will see)
 */
class MainController extends AbstractController{

    /**
     * @Route("/", name = "main_page_unlogged", methods={"GET"})
     */
    public function index(){
        // TODO: zobrazenie recently updated groups a najnovsie prispevky od users

        return $this->render('unlogged/index.html.twig');
    }


}