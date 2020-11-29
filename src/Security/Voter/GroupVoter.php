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
    // these strings are just invented: you can use anything
    const MEMBER = 'GROUP_MEMBER';
    const MOD = 'GROUP_MOD';
    const OWNER = 'GROUP_OWNER';
    const APPL = 'GROUP_APPL';
    const M_APPL = 'GROUP_MOD_APPL';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::MEMBER, self::MOD, self::OWNER, self::APPL, self::M_APPL])) {
            return false;
        }

        // only vote on `Post` objects
        if ($subject[0] instanceof Group and $subject[1] instanceof User) {
            return true;
        }

        return false;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        /*
        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }
        */

        /** @var Group $group */
        $group = $subject[0];
        /** @var User $user */
        $user = $subject[1];

        switch ($attribute) {
            case self::MEMBER:
                return $this->isMember($group, $user);
            case self::MOD:
                return $this->isMod($group, $user);
            case self::OWNER:
                return $this->isOwner($group, $user);
            case self::APPL:
                return $this->isAppl($group, $user);
            case self::M_APPL:
                return $this->isMAppl($group, $user);
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
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return $group->getAdminUser() === $user;
    }

    private function isAppl(Group $group, User $user) {
        return $group->userApplied($user);
    }

    private function isMAppl(Group $group, User $user) {
        return $group->isAppliedMod($user);
    }
}
