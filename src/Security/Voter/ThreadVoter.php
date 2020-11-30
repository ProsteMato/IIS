<?php


namespace App\Security\Voter;


use App\Entity\Thread;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * Class ThreadVoter
 *
 * Handles roles in regard with Threads
 *
 * @package App\Security\Voter
 */
class ThreadVoter extends Voter
{
    /**
     * User if owne of thread
     */
    const OWNER = 'OWNER';

    /**
     * @var Security
     */
    private $security;

    /**
     * ThreadVoter constructor.
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

        if (!$subject instanceof Thread) {
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
     * @return bool
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
     * Checks if user is owner of the thread
     *
     * @param Thread $thread target thread
     * @param User $user target user
     * @return bool true if granted, false otherwise
     */
    private function isOwner(Thread $thread, User $user) {
        return $user === $thread->getCreatedBy();
    }

}