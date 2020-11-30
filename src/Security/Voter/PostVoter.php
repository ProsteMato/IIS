<?php


namespace App\Security\Voter;


use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * Class PostVoter
 *
 * Handles roles in regard with Posts
 *
 * @package App\Security\Voter
 */
class PostVoter extends Voter
{

    /**
     * User is owner/author of posts
     */
    const OWNER = 'OWNER';

    /**
     * @var Security
     */
    private $security;

    /**
     * PostVoter constructor.
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Checks whether this class supports the attribute and subject
     *
     * @param string $attribute
     * @param mixed $subject
     * @return bool true if support, false otherwise
     */
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

    /**
     * Checks if user is owner of the post
     *
     * @param Post $post target post
     * @param User $user target user
     * @return bool true if granted, false otherwise
     */
    private function isOwner(Post $post, User $user) {
        return $user === $post->getCreatedBy();
    }
}