<?php

namespace App\Security\Voter;

use App\Entity\Group;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class GroupVoter extends Voter
{
    // these strings are just invented: you can use anything
    const MEMBER = 'GROUP_MEMBER';
    const MOD = 'GROUP_MOD';
    const OWNER = 'GROUP_OWNER';
    const APPL = 'GROUP_APPL';
    const M_APPL = 'GROUP_MOD_APPL';

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

        /** @var Post $group */
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
                return $group->userApplied($user);
            case self::M_APPL:
                return $group->isAppliedMod($user);
        }

        throw new \LogicException('This code should not be reached!');
    }
}
