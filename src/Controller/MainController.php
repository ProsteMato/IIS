<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class MainController extends AbstractController
{
    /**
    * @Route("/", name = "main_page", methods={"GET"})
    */
    public function main_page(Request $request, EntityManagerInterface $entityManager, UserInterface $loggedUser = null){
        // TODO: zobrazenie recently updated groups a najnovsie prispevky od users

        return $this->render('unlogged/index.html.twig', [
            'loggedUser' => $loggedUser
        ]);
    }

    /**
     * @Route("/acces_denied", name = "acces_denied")
     */
    public function access_denied(UserInterface $loggedUser = null){
        return $this->render('common/access_denied.html.twig', [
            'loggedUser' => $loggedUser
        ]);
    }




}
