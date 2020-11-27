<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class MainController
 *
 * Class that handles main page for both logged in and not users
 *
 * @author Magdaléna Ondrušková <xondru16@stud.fit.vutbr.cz>
 * @package App\Controller
 */
class MainController extends AbstractController
{
    /**
     * @Route("/", name = "main_page", methods={"GET"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserInterface|null $loggedUser object of logged in user - if no one is logged it is null
     * @return Response view
     */
    public function main_page(Request $request, EntityManagerInterface $entityManager, UserInterface $loggedUser = null){
        // TODO: zobrazenie recently updated groups a najnovsie prispevky od users

        return $this->render('unlogged/index.html.twig', [
            'loggedUser' => $loggedUser
        ]);
    }

    /**
     * @Route("/acces_denied", name = "acces_denied")
     * @param UserInterface|null $loggedUser
     * @return Response
     */
    public function access_denied(UserInterface $loggedUser = null){
        return $this->render('common/access_denied.html.twig', [
            'loggedUser' => $loggedUser
        ]);
    }




}
