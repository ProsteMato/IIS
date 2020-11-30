<?php


namespace App\Security\Voter;


use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class PostVoter extends Voter
{

    const OWNER = 'OWNER';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject)
    {
        if (!in_array($attribute, [self::OWNER])) {
            return false;
        }

        if (!$subject instanceof Post) {
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
            case self::OWNER:
                return $this->isOwner($subject, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function isOwner(Post $post, User $user) {
        return $user === $post->getCreatedBy();
    }
}