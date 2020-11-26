<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/group/show/{group_id}/thread", name="group.thread.")
 */
class ThreadController extends AbstractController
{

    /**
     * @Route("/create", name="create")
     * @param UserInterface $user
     */
    public function create(UserInterface $user) {

    }

    /**
     * @Route("/show/{thread_id}", name="show")
     */
    public function show() {

    }
}
