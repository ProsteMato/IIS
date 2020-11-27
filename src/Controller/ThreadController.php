<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Thread;
use App\Form\ThreadType;
use App\Repository\GroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/group/show/{group_id}/thread", name="group.thread.")
 */
class ThreadController extends AbstractController
{
    /**
     * @Route("/show/{thread_id}", name="show")
     */
    public function show() {
        return $this->render('thread/show.html.twig');
    }
}
