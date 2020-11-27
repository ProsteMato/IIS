<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Thread;
use App\Form\GroupType;
use App\Form\ThreadType;
use App\Entity\GroupUser;
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

        // Anyone can see the group
        if ($group->getVisibility() == 1){
            return $this->render('group/show.html.twig', [
                'group' => $group,
                'loggedUser' => $user,
                'users' => $users,
                'threads' => $threads,
                'form' => $form->createView()
            ]);
        } else {
            // User is in group so he can see
            if ($user != null){
                if (in_array($user, $users)){
                    return $this->render('group/show.html.twig', [
                        'group' => $group,
                        'loggedUser' => $user,
                        'users' => $users,
                        'threads' => $threads,
                        'form' => $form->createView()
                    ]);
                }
            }
            // No permission to see
            return $this->render('group/notshow.html.twig', [
                'group' => $group,
                'loggedUser' => $user
            ]);
        }
    }

    /**
     * @Route("/group/list", name="list_groups")
     */
    public function list(UserInterface $loggedUser): Response
    {
        $groups = $loggedUser->getGroups();
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
            } else {
                $group->setPicture("blank_group.png");
            }
            $group->setAdminUser($loggedUser);
            $group->setDateCreated(new DateTime());

            $groupUser = new GroupUser();
            $groupUser->setGroup($group);
            $groupUser->setUser($loggedUser);
            $groupUser->giveRole('MEM');
            $groupUser->giveRole('MOD');

            $em->persist($group);
            $em->persist($groupUser);
            $em->flush();

            return $this->redirectToRoute('show_group', ['group_id' => $group->getId()]);
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

    /**
     * @Route ("/group/{group_id}/subscribe", name="subscribe_group")
     */
    public function subscribe($group_id, GroupRepository $groupRepository, UserInterface $loggedUser = null)
    {
        $group = $groupRepository->find($group_id);
        $em = $this->getDoctrine()->getManager();

        if ($group->getOpen()){
            $gu = $group->getGroupUser();
            $newGU = new GroupUser();
            $newGU->setGroup($group);
            $newGU->setUser($loggedUser);
            $newGU->giveRole('MEM');

            $em->persist($newGU);
            $em->flush();

            return $this->redirectToRoute('show_group', ['group_id' => $group->getId()]);
        } else {
            $gu = $group->getGroupUser();
            $newGU = new GroupUser();
            $newGU->setGroup($group);
            $newGU->setUser($loggedUser);
            $newGU->giveRole('APP');

            $em->persist($newGU);
            $em->flush();

            return $this->redirectToRoute('show_group', ['group_id' => $group->getId()]);
        }

    }
    /**
     * @Route ("/group/{group_id}/unapply", name="unapply_group")
     */
    public function unapply($group_id, GroupRepository $groupRepository, UserInterface $loggedUser = null)
    {
        $group = $groupRepository->find($group_id);
        $em = $this->getDoctrine()->getManager();
        $groupUser = $group->getGroupUser();
        foreach ($groupUser as &$gu){
            if ($gu->getUser() == $loggedUser){
                $em->remove($gu);
                $em->flush();
                break;
            }
        }
        return $this->redirectToRoute('show_group', ['group_id' => $group->getId()]);
    }

    /**
     * @Route ("/group/show/{group_id}/edit", name="edit_group")
     */
    public function edit($group_id, Request $request, GroupRepository $groupRepository, UserInterface $loggedUser = null)
    {
        $group = $groupRepository->find($group_id);
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            /* @var UploadedFile $file */
            $file = $form['picture']->getData();
            if ($file) {
                $filename = md5(uniqid()) . '.' . $file->guessClientExtension();
                $file->move($this->getParameter('group_pics_dir'), $filename);
                $group->setPicture($filename);
            } else {
                $group->setPicture("blank_group.png");
            }
            $group->setAdminUser($loggedUser);
            $group->setDateCreated(new DateTime());

            $groupUser = new GroupUser();
            $groupUser->setGroup($group);
            $groupUser->setUser($loggedUser);
            $groupUser->giveRole('MEM');
            $groupUser->giveRole('MOD');

            $em->persist($group);
            $em->persist($groupUser);
            $em->flush();

            return $this->redirectToRoute('show_group', ['group_id' => $group->getId()]);
        }

        return $this->render('group/edit.html.twig', [
            'form' => $form->createView(),
            'group' => $group,
            'loggedUser' => $loggedUser
        ]);
    }

    /**
     * @Route ("/group/show/{group_id}/leave", name="leave_group")
     */
    public function leave($group_id,  GroupRepository $groupRepository, UserInterface $loggedUser = null)
    {
        $group = $groupRepository->find($group_id);
        if ($group->getAdminUser() == $loggedUser){
            $this->addFlash("notice", "You have to change admin of group first in order to leave");
            return $this->redirectToRoute('show_group', ['group_id' => $group->getId()]);
        }
        $em = $this->getDoctrine()->getManager();
        $groupUser = $group->getGroupUser();
        foreach ($groupUser as &$gu){
            if ($gu->getUser() == $loggedUser){
                $em->remove($gu);
                $em->flush();
                break;
            }
        }
        return $this->redirectToRoute('show_group', ['group_id' => $group->getId()]);
    }
}
