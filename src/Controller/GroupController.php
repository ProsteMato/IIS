<?php

namespace App\Controller;

use App\Entity\Group;
use App\Form\GroupType;
use App\Repository\GroupRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class GroupController extends AbstractController
{
    /**
     * @Route("/group/show/{group_id}", name="show_group")
     */
    public function show($group_id, GroupRepository $groupRepository, UserInterface $loggedUser): Response
    {
        $group = $groupRepository->find($group_id);
        $users = $group->getUsers();

        return $this->render('group/show.html.twig', [
            'group' => $group,
            'loggedUser' => $loggedUser,
            'users' => $users
        ]);
    }

    /**
     * @Route("/group/list", name="list_groups")
     */
    public function list(UserInterface $loggedUser): Response
    {
        $groups = $loggedUser->getLikedGroups();
        return $this->render('group/list.html.twig', [
            'loggedUser' => $loggedUser,
            'groups' => $groups
        ]);
    }

    /**
     * @Route("/group/create", name="create_group")
     */
    public function create(Request $request, UserInterface $loggedUser): Response
    {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            /* @var UploadedFile $file */
            $file = $form['picture']->getData();
            if ($file) {
                $filename = md5(uniqid()) . '.' . $file->guessClientExtension();
                $file->move($this->getParameter('group_pics_dir'), $filename);
                $group->setPicture($filename);
            }
            $group->setAdminUser($loggedUser);
            $group->setDateCreated(new DateTime());
            $group->addUser($loggedUser);
            $em->persist($group);
            $em->flush();

            return $this->render('group/show.html.twig', [
                'group' => $group,
                'loggedUser' => $loggedUser
            ]);
        }

        return $this->render('group/create.html.twig', [
            'form' => $form->createView(),
            'loggedUser' => $loggedUser
        ]);
    }

    /**
     * @Route ("/group/delete/{id}", name="delete_group")
     */
    public function remove(Group $group)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($group);
        $em->flush();

        $this->addFlash('success', 'Post was removed');

        return $this->redirect($this->generateUrl('post.index'));
    }
}
