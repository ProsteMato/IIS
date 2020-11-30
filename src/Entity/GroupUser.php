<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * Class representing relation between group and user
 *
 * @ORM\Entity
 * @ORM\Table(name="group_user")
 */
class GroupUser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $group;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="groups")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="array")
     */
    private $role = [];

    /**
     * Getter for id of relation
     *
     * @return int id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Getter for group
     *
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Setter for group
     *
     * @param Group $group
     */
    public function setGroup($group): void
    {
        $this->group = $group;
    }

    /**
     * Getter for user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Setter for user
     *
     * @param User $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * Returns roles
     *
     * @return string[] roles of userin group
     */
    public function getRole(): array
    {
        return $this->role;
    }

    /**
     * Adds role to the member of froup
     *
     * @param string $role role to be added
     */
    public function giveRole(string $role): void
    {
        if (!in_array($role, $this->role)){
            array_push($this->role, $role);
        }
    }

    /**
     * Removes role from member of group
     *
     * @param string $role role to be removed
     */
    public function removeRole(string $role): void
    {
        if (($key = array_search($role, $this->role)) !== false) {
            unset($this->role[$key]);
        }
    }

}