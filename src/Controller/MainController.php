<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
    * @Route("/", name = "main_page_unlogged", methods={"GET"})
    */
    public function unlogged(){
        // TODO: zobrazenie recently updated groups a najnovsie prispevky od users

        return $this->render('unlogged/index.html.twig');
    }


}
