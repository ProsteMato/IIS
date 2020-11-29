<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Thread;
use App\Form\ChangeOwnerType;
use App\Form\GroupType;
use App\Form\ThreadType;
use App\Entity\GroupUser;
use App\Repository\GroupRepository;
use App\Repository\ThreadRepository;
use App\Repository\UserRepository;
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
    public function create(Request $request, GroupRepository $groupRepository, UserInterface $loggedUser = null): Response
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
            $groupUser->giveRole('ROLE_MEM');
            $groupUser->giveRole('ROLE_MOD');

            $em->persist($group);
            $em->persist($groupUser);
            $em->flush();

            return $this->redirectToRoute('show_group', [
                'group_id' => $group->getId()
            ]);
        }

        return $this->render('group/create.html.twig', [
            'form' => $form->createView(),
            'loggedUser' => $loggedUser
        ]);
    }

    /**
     * @Route ("/group/show/{id}/edit/delete", name="delete_group")
     */
    public function delete_group(Group $group)
    {
        // aj s threadmi a postmi !!


        $em = $this->getDoctrine()->getManager();
        $groupUser = $group->getGroupUser();
        foreach ($groupUser as &$gu){
            $em->remove($gu);
        }

        $threads = $group->getThreads();
        foreach ($threads as &$thread){
            foreach ($thread->getPosts() as &$post){
                $em->remove($post);
            }
            foreach ($thread->getPostUsers() as &$pu){
                $em->remove($pu);
            }
            foreach ($thread->getThreadUsers() as &$tu){
                $em->remove($tu);
            }
            $em->remove($thread);
        }
        $em->remove($group);
        $em->flush();

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
            $newGU->giveRole('ROLE_MEM');

            $em->persist($newGU);
            $em->flush();

            return $this->redirectToRoute('show_group', ['group_id' => $group->getId()]);
        } else {
            $gu = $group->getGroupUser();
            $newGU = new GroupUser();
            $newGU->setGroup($group);
            $newGU->setUser($loggedUser);
            $newGU->giveRole('ROLE_APP');

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
        $form = $this->createForm(GroupType::class, $group, ['label' => 'Edit group']);
        $form->handleRequest($request);

        $formOwner = $this->createForm(ChangeOwnerType::class);
        $formOwner->handleRequest($request);


        $applications = $group->getAppliedUsers();
        $modApps = $group->getAppliedMods();
        $moderators = $group->getMods();

        if (($key = array_search($loggedUser, $moderators)) !== false) {
            unset($moderators[$key]);
        }

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
            $em->persist($group);
            $em->flush();
            return $this->redirectToRoute('show_group', ['group_id' => $group->getId()]);
        }

        if($formOwner->isSubmitted() && $formOwner->isValid()) {
            $formData = $formOwner->getData();
            return $this->redirectToRoute('group_change_owner', ['group_id' => $group->getId(), 'email'=>$formData['email']]);
        }

        return $this->render('group/edit.html.twig', [
            'form' => $form->createView(),
            'ownerForm' => $formOwner->createView(),
            'group' => $group,
            'loggedUser' => $loggedUser,
            'applications' => $applications,
            'appsCount' => count($applications),
            'modApps' => $modApps,
            'modAppsCount' => count($modApps),
            'moderators' => $moderators,
            'modCount' => count($moderators)
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

    /**
     * @Route ("/group/show/{group_id}/edit/mod/delete/{user_id}", name="group_delete_mod")
     */
    public function delete_mod($group_id,  $user_id, GroupRepository $groupRepository, UserRepository $userRepository,
                               UserInterface $loggedUser = null)
    {
        $group = $groupRepository->find($group_id);
        $groupUser = $group->getGroupUser();
        $user = $userRepository->find($user_id);
        foreach ($groupUser as &$gu){
            if ($gu->getUser() == $user){
                $gu->removeRole('ROLE_MOD');
                $em = $this->getDoctrine()->getManager();
                $em->persist($gu);
                $em->flush();
                return $this->redirectToRoute('edit_group', ['group_id' => $group->getId()]);
            }
        }
        return $this->redirectToRoute('edit_group', ['group_id' => $group->getId()]);
    }

    /**
     * @Route ("/group/show/{group_id}/unapplymod", name="group_unapply_mod")
     */
    public function unapply_mod($group_id, GroupRepository $groupRepository, UserRepository $userRepository, UserInterface $loggedUser = null)
    {
        $group = $groupRepository->find($group_id);
        $groupUser = $group->getGroupUser();
        foreach ($groupUser as &$gu){
            if ($gu->getUser() == $loggedUser){
                $gu->removeRole('ROLE_MAPP');
                $em = $this->getDoctrine()->getManager();
                $em->persist($gu);
                $em->flush();
                return $this->redirectToRoute('show_group', [
                    'group_id' => $group_id
                ]);
            }
        }
        return $this->redirectToRoute('show_group', [
            'group_id' => $group_id
        ]);
    }

    /**
     * @Route ("/group/show/{group_id}/revokemod", name="group_revoke_mod")
     */
    public function revoke_mod($group_id, GroupRepository $groupRepository, UserRepository $userRepository, UserInterface $loggedUser = null)
    {
        $group = $groupRepository->find($group_id);
        $groupUser = $group->getGroupUser();
        foreach ($groupUser as &$gu){
            if ($gu->getUser() == $loggedUser){
                $gu->removeRole('ROLE_MOD');
                $em = $this->getDoctrine()->getManager();
                $em->persist($gu);
                $em->flush();
                return $this->redirectToRoute('show_group', [
                    'group_id' => $group_id
                ]);
            }
        }
        return $this->redirectToRoute('show_group', [
            'group_id' => $group_id
        ]);
    }

    /**
     * @Route ("/group/show/{group_id}/applymod", name="group_apply_mod")
     */
    public function apply_mod($group_id, GroupRepository $groupRepository, UserRepository $userRepository, UserInterface $loggedUser = null)
    {
        $group = $groupRepository->find($group_id);
        $groupUser = $group->getGroupUser();
        foreach ($groupUser as &$gu){
            if ($gu->getUser() == $loggedUser){
                $gu->giveRole('ROLE_MAPP');
                $em = $this->getDoctrine()->getManager();
                $em->persist($gu);
                $em->flush();
                return $this->redirectToRoute('show_group', [
                    'group_id' => $group_id
                ]);
            }
        }
        return $this->redirectToRoute('show_group', [
            'group_id' => $group_id
        ]);
    }

    /**
     * @Route ("/group/show/{group_id}/edit/mod/accept/{user_id}", name="group_accept_mod")
     */
    public function accept_mod($group_id,  $user_id, GroupRepository $groupRepository, UserRepository $userRepository,
                               UserInterface $loggedUser = null)
    {
        $group = $groupRepository->find($group_id);
        $groupUser = $group->getGroupUser();
        $user = $userRepository->find($user_id);
        foreach ($groupUser as &$gu){
            if ($gu->getUser() == $user){
                $gu->giveRole('ROLE_MOD');
                $gu->removeRole('ROLE_MAPP');
                $em = $this->getDoctrine()->getManager();
                $em->persist($gu);
                $em->flush();
                return $this->redirectToRoute('edit_group', ['group_id' => $group->getId()]);
            }
        }
        return $this->redirectToRoute('edit_group', ['group_id' => $group->getId()]);
    }

    /**
     * @Route ("/group/show/{group_id}/edit/mod/deny/{user_id}", name="group_deny_mod")
     */
    public function deny_mod($group_id,  $user_id, GroupRepository $groupRepository, UserRepository $userRepository,
                             UserInterface $loggedUser = null)
    {
        $group = $groupRepository->find($group_id);
        $groupUser = $group->getGroupUser();
        $user = $userRepository->find($user_id);
        foreach ($groupUser as &$gu){
            if ($gu->getUser() == $user){
                $gu->removeRole('ROLE_MAPP');
                $em = $this->getDoctrine()->getManager();
                $em->persist($gu);
                $em->flush();
                return $this->redirectToRoute('edit_group', ['group_id' => $group->getId()]);
            }
        }
        return $this->redirectToRoute('edit_group', ['group_id' => $group->getId()]);
    }

    /**
     * @Route ("/group/show/{group_id}/edit/apps/accep/{user_id}", name="group_app_accept")
     */
    public function appl_accept($group_id,  $user_id, GroupRepository $groupRepository, UserRepository $userRepository,
                                UserInterface $loggedUser = null)
    {
        $group = $groupRepository->find($group_id);
        $groupUser = $group->getGroupUser();
        $user = $userRepository->find($user_id);
        foreach ($groupUser as &$gu){
            if ($gu->getUser() == $user){
                $gu->giveRole('ROLE_MEM');
                $gu->removeRole('ROLE_APP');
                $em = $this->getDoctrine()->getManager();
                $em->persist($gu);
                $em->flush();
                return $this->redirectToRoute('edit_group', ['group_id' => $group->getId()]);
            }
        }
        return $this->redirectToRoute('edit_group', ['group_id' => $group->getId()]);
    }

    /**
     * @Route ("/group/show/{group_id}/edit/apps/deny/{user_id}", name="group_app_deny")
     */
    public function appl_deny($group_id, $user_id, UserRepository $userRepository, GroupRepository $groupRepository,
                              UserInterface $loggedUser = null)
    {
        $group = $groupRepository->find($group_id);
        $groupUser = $group->getGroupUser();
        $user = $userRepository->find($user_id);
        foreach ($groupUser as &$gu){
            if ($gu->getUser() == $user){
                $em = $this->getDoctrine()->getManager();
                $em->remove($gu);
                $em->persist($group);
                $em->flush();
                return $this->redirectToRoute('edit_group', ['group_id' => $group->getId()]);
            }
        }
        return $this->redirectToRoute('edit_group', ['group_id' => $group->getId()]);
    }

    /**
     * @Route ("/group/show/{group_id}/edit/changeowner/{email}", name="group_change_owner")
     */
    public function change_owner($group_id, $email, UserRepository $userRepository, GroupRepository $groupRepository,
                              UserInterface $loggedUser = null)
    {
        $group = $groupRepository->find($group_id);
        $newOwner = $userRepository->findByEmail($email);
        if (!$newOwner) {
            $this->addFlash("notice", "User not found");
            return $this->redirectToRoute('edit_group', ['group_id' => $group->getId()]);
        } elseif (!$group->isMember($newOwner[0])) {
            $this->addFlash("notice", "User has to be member of group");
            return $this->redirectToRoute('edit_group', ['group_id' => $group->getId()]);
        } else {
            $group->setAdminUser($newOwner[0]);
            $groupUser = $group->getGroupUser();
            $em = $this->getDoctrine()->getManager();
            foreach ($groupUser as &$gu) {
                if ($gu->getUser() == $newOwner[0]) {
                    $gu->giveRole('ROLE_MEM');
                    $em->persist($gu);
                    break;
                }
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($group);
            $em->flush();
            return $this->redirectToRoute('show_group', ['group_id' => $group->getId()]);
        }
    }

    /**
     * @Route ("/group/show/{group_id}/kick/{user_id}", name="group_kick_user")
     */
    public function group_kick($group_id, $user_id, UserRepository $userRepository, GroupRepository $groupRepository,
                                UserInterface $loggedUser)
    {
        $group = $groupRepository->find($group_id);
        $user = $userRepository->find($user_id);

        if ($group->getAdminUser() == $user){
            $this->addFlash("notice", "You have to change admin of group first in order to leave");
            return $this->redirectToRoute('show_group', ['group_id' => $group->getId()]);
        }
        $em = $this->getDoctrine()->getManager();
        $groupUser = $group->getGroupUser();
        foreach ($groupUser as &$gu){
            if ($gu->getUser() == $user){
                $em->remove($gu);
                $em->flush();
                break;
            }
        }
        return $this->redirectToRoute('show_group', ['group_id' => $group->getId()]);
    }
}
