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

/**
 * Class GroupController handles everything related to groups
 * @package App\Controller
 */
class GroupController extends AbstractController
{
    /**
     * @Route("/group/show/{group_id}", name="show_group")
     *
     * Shows group page if loggedUser can view it, otherwise redirects to noshow page
     *
     * @param string $group_id id of group
     * @param Request $request
     * @param GroupRepository $groupRepository
     * @param UserInterface|null $user
     * @return Response
     */
    public function show(string $group_id, Request $request, GroupRepository $groupRepository, UserInterface $user = null): Response
    {
        $group = $groupRepository->find($group_id);
        $users = $group->getUsers();
        $otherUsers = $group->getOtherUsers();
        $mods = $group->getMods();

        $threads = $group->getThreads();
        $thread = new Thread();

        $form = $this->createForm(ThreadType::class, $thread);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $thread = $form->getData();
            $thread->setCreationDate(new \DateTime('now'));
            $thread->setCreatedBy($user);
            $thread->setRating(0);
            $thread->setViews(0);
            $thread->setGroupId($groupRepository->find($group_id));
            $em = $this->getDoctrine()->getManager();
            $em->persist($thread);
            $em->flush();

            return $this->redirectToRoute('show_group', [
                'group_id' => $group_id
            ]);
        }

        // Anyone can see the group
        if ($group->getVisibility() == 1 or $this->isGranted('ROLE_ADMIN')){
            return $this->render('group/show.html.twig', [
                'group' => $group,
                'loggedUser' => $user,
                'otherUsers' => $otherUsers,
                'mods' => $mods,
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
                        'otherUsers' => $otherUsers,
                        'mods' => $mods,
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
     *
     * lists users groups
     *
     * @param UserInterface $loggedUser
     * @return Response
     */
    public function list(UserInterface $loggedUser): Response
    {
        if(!$this->isGranted('ROLE_USER')){
            throw $this->createAccessDeniedException('not allowed');
        }

        $groups = $loggedUser->getGroups();

        return $this->render('group/list.html.twig', [
            'loggedUser' => $loggedUser,
            'groups' => $groups
        ]);
    }


    /**
     * @Route("/group/create", name="create_group")
     *
     * Controller for new group creation
     *
     * @param Request $request
     * @param GroupRepository $groupRepository
     * @param UserInterface|null $loggedUser
     * @return Response
     */
    public function create(Request $request, GroupRepository $groupRepository, UserInterface $loggedUser = null): Response
    {

        if(!$this->isGranted('ROLE_USER')){
            throw $this->createAccessDeniedException('not allowed');
        }

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
     *
     * Controller for group deletion
     *
     * @param Group $group
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete_group(Group $group)
    {
        if(!$this->isGranted('ROLE_ADMIN') and !$this->isGranted('GROUP_OWNER', [$group, $loggedUser])){
            throw $this->createAccessDeniedException('not allowed');
        }

        $em = $this->getDoctrine()->getManager();
        $groupUser = $group->getGroupUser();
        foreach ($groupUser as &$gu){
            $em->remove($gu);
        }

        $threads = $group->getThreads();
        foreach ($threads as $thread){
            foreach ($thread->getPosts() as $post){
                $em->remove($post);
            }
            foreach ($thread->getPostUsers() as $pu){
                $em->remove($pu);
            }
            foreach ($thread->getThreadUsers() as $tu){
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
     *
     * User will submit application to join the group
     *
     * @param string $group_id id of group
     * @param GroupRepository $groupRepository
     * @param UserInterface|null $loggedUser
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function subscribe(string $group_id, GroupRepository $groupRepository, UserInterface $loggedUser = null)
    {
        $group = $groupRepository->find($group_id);

        if($this->isGranted('GROUP_MEMBER', [$group, $loggedUser])){
            throw $this->createAccessDeniedException('not allowed');
        }

        $em = $this->getDoctrine()->getManager();

        if ($group->getOpen() or $this->isGranted('ROLE_ADMIN')){
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
     *
     * User will delete his application to join group
     *
     * @param string $group_id id of group
     * @param GroupRepository $groupRepository
     * @param UserInterface|null $loggedUser
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function unapply($group_id, GroupRepository $groupRepository, UserInterface $loggedUser = null)
    {
        $group = $groupRepository->find($group_id);

        if(!$this->isGranted('GROUP_APPL', [$group, $loggedUser])){
            throw $this->createAccessDeniedException('not allowed');
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
     * @Route ("/group/show/{group_id}/edit", name="edit_group")
     *
     * Open group setting window
     *
     * @param string $group_id id of group
     * @param Request $request
     * @param GroupRepository $groupRepository
     * @param UserInterface|null $loggedUser
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function edit(string $group_id, Request $request, GroupRepository $groupRepository, UserInterface $loggedUser = null)
    {
        $group = $groupRepository->find($group_id);

        if(!$this->isGranted('ROLE_ADMIN') and !$this->isGranted('GROUP_OWNER', [$group, $loggedUser])
            and !$this->isGranted('GROUP_MOD', [$group, $loggedUser])){
            throw $this->createAccessDeniedException('not allowed');
        }

        $form = $this->createForm(GroupType::class, $group, ['label' => 'Edit group']);
        $form->handleRequest($request);

        $formOwner = $this->createForm(ChangeOwnerType::class);
        $formOwner->handleRequest($request);


        $applications = $group->getAppliedUsers();
        $modApps = $group->getAppliedMods();
        $moderators = $group->getMods();
        $members = $group->getUsers();


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
            'members' => $members,
            'membersCount' => count($members),
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
     *
     * Logged user will leave group
     *
     * @param string $group_id id of group
     * @param GroupRepository $groupRepository
     * @param UserInterface|null $loggedUser
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function leave(string $group_id, GroupRepository $groupRepository, UserInterface $loggedUser = null)
    {
        $group = $groupRepository->find($group_id);

        if(!$this->isGranted('GROUP_MEMBER', [$group, $loggedUser])){
            throw $this->createAccessDeniedException('not allowed');
        }

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
     *
     * Remove moderator role from user who is moderator in the group
     *
     * @param string $group_id id of group
     * @param string $user_id id of user who will be stripped of moderator role
     * @param GroupRepository $groupRepository
     * @param UserRepository $userRepository
     * @param UserInterface|null $loggedUser
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete_mod($group_id, $user_id, GroupRepository $groupRepository, UserRepository $userRepository,
                               UserInterface $loggedUser = null)
    {
        $group = $groupRepository->find($group_id);

        if(!$this->isGranted('ROLE_ADMIN') and !$this->isGranted('GROUP_OWNER', [$group, $loggedUser])){
            throw $this->createAccessDeniedException('not allowed');
        }

        $groupUser = $group->getGroupUser();
        $user = $userRepository->find($user_id);
        foreach ($groupUser as &$gu){
            if ($gu->getUser() == $user){
                $gu->removeRole('ROLE_MOD');
                $em = $this->getDoctrine()->getManager();
                $em->persist($gu);
                $em->flush();
                if ($user == $loggedUser)
                {
                    return $this->redirectToRoute('show_group', ['group_id' => $group->getId()]);

                } else {
                    return $this->redirectToRoute('edit_group', ['group_id' => $group->getId()]);
                }

            }
        }
        return $this->redirectToRoute('edit_group', ['group_id' => $group->getId()]);
    }


    /**
     * @Route ("/group/show/{group_id}/unapplymod", name="group_unapply_mod")
     *
     * Remove loggedUsers application to become moderator of group
     *
     * @param string $group_id id of group
     * @param GroupRepository $groupRepository
     * @param UserRepository $userRepository
     * @param UserInterface|null $loggedUser
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function unapply_mod(string $group_id, GroupRepository $groupRepository, UserRepository $userRepository, UserInterface $loggedUser = null)
    {
        $group = $groupRepository->find($group_id);

        if(!($this->isGranted('GROUP_MEMBER', [$group, $loggedUser]) and !$this->isGranted('GROUP_MOD_APPL', [$group, $loggedUser]))){
            throw $this->createAccessDeniedException('not allowed');
        }


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
     *
     * Revoke moderator role of loggedUser in the the group
     *
     * @param string $group_id group id
     * @param GroupRepository $groupRepository
     * @param UserRepository $userRepository
     * @param UserInterface|null $loggedUser
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function revoke_mod(string $group_id, GroupRepository $groupRepository, UserRepository $userRepository, UserInterface $loggedUser = null)
    {
        $group = $groupRepository->find($group_id);

        if(!$this->isGranted('ROLE_ADMIN') and !$this->isGranted('GROUP_OWNER', [$group, $loggedUser])){
            throw $this->createAccessDeniedException('not allowed');
        }

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
     *
     * Submit application of loggedUser to become moderator of group
     *
     * @param string $group_id group id
     * @param GroupRepository $groupRepository
     * @param UserRepository $userRepository
     * @param UserInterface|null $loggedUser
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function apply_mod(string $group_id, GroupRepository $groupRepository, UserRepository $userRepository, UserInterface $loggedUser = null)
    {
        $group = $groupRepository->find($group_id);

        if(!($this->isGranted('GROUP_MEMBER', [$group, $loggedUser]) and $this->isGranted('ROLE_MOD_APPL', [$group, $loggedUser]))){
            throw $this->createAccessDeniedException('not allowed');
        }

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
     *
     * Function to accpet application of user to become group moderator
     *
     * @param string $group_id group id
     * @param string $user_id id of user who submited application
     * @param GroupRepository $groupRepository
     * @param UserRepository $userRepository
     * @param UserInterface|null $loggedUser
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function accept_mod(string $group_id, string $user_id, GroupRepository $groupRepository, UserRepository $userRepository,
                               UserInterface $loggedUser = null)
    {
        $group = $groupRepository->find($group_id);

        if(!$this->isGranted('ROLE_ADMIN') and !$this->isGranted('GROUP_OWNER', [$group, $loggedUser])){
            throw $this->createAccessDeniedException('not allowed');
        }

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
     * @Route ("/group/show/{group_id}/edit/mod/give/{user_id}", name="group_give_mod")
     *
     * Function to give moderator privileges to user
     *
     * @param string $group_id group id
     * @param string $user_id user who will be given moderator role
     * @param GroupRepository $groupRepository
     * @param UserRepository $userRepository
     * @param UserInterface|null $loggedUser
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function give_mod(string $group_id, string $user_id, GroupRepository $groupRepository, UserRepository $userRepository,
                             UserInterface $loggedUser = null)
    {
        $group = $groupRepository->find($group_id);

        if(!$this->isGranted('ROLE_ADMIN') and !$this->isGranted('GROUP_OWNER', [$group, $loggedUser])){
            throw $this->createAccessDeniedException('not allowed');
        }

        $groupUser = $group->getGroupUser();
        $user = $userRepository->find($user_id);
        foreach ($groupUser as &$gu){
            if ($gu->getUser() == $user){
                $gu->giveRole('ROLE_MOD');
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
     *
     * Function to deny application to became moderator of group
     *
     * @param string $group_id id of group
     * @param string $user_id id of user who created application
     * @param GroupRepository $groupRepository
     * @param UserRepository $userRepository
     * @param UserInterface|null $loggedUser
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deny_mod(string $group_id, string $user_id, GroupRepository $groupRepository, UserRepository $userRepository,
                             UserInterface $loggedUser = null)
    {
        $group = $groupRepository->find($group_id);

        if(!$this->isGranted('ROLE_ADMIN') and !$this->isGranted('GROUP_OWNER', [$group, $loggedUser])){
            throw $this->createAccessDeniedException('not allowed');
        }

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
     *
     * Function for accepting application to join group
     *
     * @param string $group_id id of group
     * @param string $user_id id of user who created application
     * @param GroupRepository $groupRepository
     * @param UserRepository $userRepository
     * @param UserInterface|null $loggedUser
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function appl_accept(string $group_id, string $user_id, GroupRepository $groupRepository, UserRepository $userRepository,
                                UserInterface $loggedUser = null)
    {
        $group = $groupRepository->find($group_id);

        if(!$this->isGranted('ROLE_ADMIN') and !$this->isGranted('GROUP_OWNER', [$group, $loggedUser])
            and !$this->isGranted('GROUP_MOD', [$group, $loggedUser])){
            throw $this->createAccessDeniedException('not allowed');
        }

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
     *
     * Function for denying application to join group
     *
     * @param string $group_id id of group
     * @param string $user_id id of user who created application
     * @param UserRepository $userRepository
     * @param GroupRepository $groupRepository
     * @param UserInterface|null $loggedUser
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function appl_deny(string $group_id, string $user_id, UserRepository $userRepository, GroupRepository $groupRepository,
                              UserInterface $loggedUser = null)
    {
        $group = $groupRepository->find($group_id);

        if(!$this->isGranted('ROLE_ADMIN') and !$this->isGranted('GROUP_OWNER', [$group, $loggedUser])
                and !$this->isGranted('GROUP_MOD', [$group, $loggedUser])){
            throw $this->createAccessDeniedException('not allowed');
        }

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
     *
     * Function for changing owner of group
     *
     * @param string $group_id group id
     * @param string $email email of new owner user
     * @param UserRepository $userRepository
     * @param GroupRepository $groupRepository
     * @param UserInterface|null $loggedUser
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function change_owner(string $group_id, string $email, UserRepository $userRepository, GroupRepository $groupRepository,
                                 UserInterface $loggedUser = null)
    {
        $group = $groupRepository->find($group_id);

        if(!$this->isGranted('ROLE_ADMIN') and !$this->isGranted('GROUP_OWNER', [$group, $loggedUser])){
            throw $this->createAccessDeniedException('not allowed');
        }

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
     *
     * Funtion for kicking user from group
     *
     * @param string $group_id id of group
     * @param string $user_id id of user
     * @param UserRepository $userRepository
     * @param GroupRepository $groupRepository
     * @param UserInterface $loggedUser
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function group_kick(string $group_id, string $user_id, UserRepository $userRepository, GroupRepository $groupRepository,
                               UserInterface $loggedUser)
    {
        $group = $groupRepository->find($group_id);

        if(!$this->isGranted('ROLE_ADMIN') and !$this->isGranted('GROUP_OWNER', [$group, $loggedUser])
            and !$this->isGranted('GROUP_MOD', [$group, $loggedUser])){
            throw $this->createAccessDeniedException('not allowed');
        }

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
