<?php

namespace App\Security\Voter;

use App\Entity\Group;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class GroupVoter extends Voter
{
    const MEMBER = 'GROUP_MEMBER';
    const MOD = 'GROUP_MOD';
    const OWNER = 'GROUP_OWNER';
    const APPL = 'GROUP_APPL';
    const M_APPL = 'GROUP_MOD_APPL';
    const VIEW = 'GROUP_VIEW';


    protected function supports(string $attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::MEMBER, self::MOD, self::OWNER, self::APPL, self::M_APPL, self::VIEW])) {
            return false;
        }
        // if the subject isn't one we support, return false
        if ($subject[0] instanceof Group and $subject[1] instanceof User) {
            return true;
        }

        return false;
    }

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

    private function isMember(Group $group, User $user) {
        if ($this->isOwner($group, $user) || $this->isMod($group, $user)) {
            return true;
        }

        return $group->isMember($user);
    }

    private function isMod(Group $group, User $user) {
        if ($this->isOwner($group, $user)) {
            return true;
        }

        return $group->isMOD($user);
    }

    private function isOwner(Group $group, User $user) {
        return $group->getAdminUser() === $user;
    }

    private function isAppl(Group $group, User $user) {
        return $group->userApplied($user);
    }

    private function isMAppl(Group $group, User $user) {
        return $group->isAppliedMod($user);
    }

    private function canView(Group $group, User $user) {
        return $group->getVisibility() or $group->isMember($user);
    }
}
