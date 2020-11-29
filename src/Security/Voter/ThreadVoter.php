<?php


namespace App\Security\Voter;


use App\Entity\Thread;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class ThreadVoter extends Voter
{
    const DELETE = 'OWNER_DELETE';
    const EDIT = 'OWNER_EDIT';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject)
    {
        if (!in_array($attribute, [self::DELETE, self::EDIT])) {
            return false;
        }

        if (!$subject instanceof Thread) {
            return false;
        }

        return true;
    }


    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($subject, $user);
            case self::DELETE:
                return $this->canDelete($subject, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(Thread $thread, User $user) {
        return $user === $thread->getCreatedBy();
    }

    private function canDelete(Thread $thread, User $user) {
        return $user === $thread->getCreatedBy();
    }

}