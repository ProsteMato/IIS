<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Thread;
use App\Form\GroupType;
use App\Form\ThreadType;
use App\Repository\GroupRepository;
use App\Repository\ThreadRepository;
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
    public function show($group_id, Request $request, GroupRepository $groupRepository, UserInterface $user = null): Response
    {
        $group = $groupRepository->find($group_id);
        $users = $group->getUsers();
        $threads = $group->getThreads();

        $thread = new Thread();

        $form = $this->createForm(ThreadType::class, $thread);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $thread = $form->getData();
            $thread->setCreationDate(new \DateTime('now'));
            $thread->setCreatedBy($user);
            $thread->setRating(0);
            $thread->setGroupId($groupRepository->find($group_id));
            $em = $this->getDoctrine()->getManager();
            $em->persist($thread);
            $em->flush();

            return $this->redirectToRoute('show_group', [
                'group_id' => $group_id
            ]);
        }

        return $this->render('group/show.html.twig', [
            'group' => $group,
            'loggedUser' => $user,
            'users' => $users,
            'threads' => $threads,
            'form' => $form->createView()
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

            $users = $group->getUsers();
            return $this->render('group/show.html.twig', [
                'group' => $group,
                'loggedUser' => $loggedUser,
                'users' => $users
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

        if ($this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('show_list_groups');
        }
        else {
            return $this->redirectToRoute('list_groups');
        }

    }
}
