<?php

namespace App\Security\Voter;

use App\Entity\Group;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class GroupVoter
 *
 * Handles roles in regard with Groups
 *
 * @package App\Security\Voter
 */
class GroupVoter extends Voter
{
    /**
     * User is member of group
     */
    const MEMBER = 'GROUP_MEMBER';

    /**
     * User is moderator of group
     */
    const MOD = 'GROUP_MOD';

    /**
     * User is owner of group
     */
    const OWNER = 'GROUP_OWNER';

    /**
     * User has submitted join application to group
     */
    const APPL = 'GROUP_APPL';

    /**
     * User has submitted application to to become moderator of group
     */
    const M_APPL = 'GROUP_MOD_APPL';

    /**
     * User may view group
     */
    const VIEW = 'GROUP_VIEW';


    /**
     * Checks whether this class supports the attribute and subject
     *
     * @param string $attribute
     * @param mixed $subject
     * @return bool true if support, false otherwise
     */
    protected function supports(string $attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::MEMBER, self::MOD, self::OWNER, self::APPL, self::M_APPL, self::VIEW])) {
            return false;
        }
        // if the subject isn't one we support, return false
        if ($subject[0] instanceof Group) {
            return true;
        }

        return false;
    }

    /**
     * Vote on attribute, calls further check functions for each attribute
     *
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool true if granted, false otherwise
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        /** @var Group $group */
        $group = $subject[0];
        /** @var User $user */
        $user = $subject[1];

        switch ($attribute) {
            case self::MEMBER:
                return $group->isMember($user);
            case self::MOD:
                return $group->isMOD($user);
            case self::OWNER:
                return $group->getAdminUser() == $user;
            case self::APPL:
                return $this->isAppl($group, $user);
            case self::M_APPL:
                return $this->isMAppl($group, $user);
            case self::VIEW:
                return $this->canView($group, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    /**
     * Checks if user is member of group
     *
     * @param Group $group target group
     * @param User $user target user
     * @return bool true if granted, false otherwise
     */
    private function isMember(Group $group, User $user) {
        if ($this->isOwner($group, $user) || $this->isMod($group, $user)) {
            return true;
        }

        return $group->isMember($user);
    }

    /**
     * Checks if user is moderator of group
     *
     * @param Group $group target group
     * @param User $user target user
     * @return bool true if granted, false otherwise
     */
    private function isMod(Group $group, User $user) {
        if ($this->isOwner($group, $user)) {
            return true;
        }

        return $group->isMOD($user);
    }

    /**
     * Checks if user is owner of group
     *
     * @param Group $group target group
     * @param User $user target user
     * @return bool true if granted, false otherwise
     */
    private function isOwner(Group $group, User $user) {
        return $group->getAdminUser() === $user;
    }

    /**
     * Checks if user has applied to join the group
     *
     * @param Group $group target group
     * @param User $user target user
     * @return bool true if granted, false otherwise
     */
    private function isAppl(Group $group, User $user) {
        return $group->userApplied($user);
    }

    /**
     * Checks if user has applied to become moderator of group
     *
     * @param Group $group target group
     * @param User $user target user
     * @return bool true if granted, false otherwise
     */
    private function isMAppl(Group $group, User $user) {
        return $group->isAppliedMod($user);
    }

    /**
     * Checks if user can view the the group
     *
     * @param Group $group target group
     * @param User $user target user
     * @return bool true if granted, false otherwise
     */
    private function canView(Group $group, User $user = null) {
        if ($user == null){
            return $group->getVisibility();
        }
        return $group->getVisibility() or $group->isMember($user);
    }
}
