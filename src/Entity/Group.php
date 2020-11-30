<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GroupRepository::class)
 * @ORM\Table(name="`group`")
 */
class Group
{
    /**
     * Unique identifier of object in database
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Name of group
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull
     */
    private $name;

    /**
     * Visibility of group
     * true = visible
     * false = invisible
     *
     * @ORM\Column(type="boolean")
     */
    private $visibility;

    /**
     * Description text o group
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * Date of creation of group
     *
     * @ORM\Column(type="datetime")
     */
    private $date_created;

    /**
     * Filename of group picture
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture;

    /**
     * Owner of group
     *
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="admin_for_groups")
     * @ORM\JoinColumn(nullable=false)
     */
    private $admin_user;

    /**
     * Members of group
     *
     * @ORM\OneToMany(targetEntity=GroupUser::class, mappedBy="group", fetch="EXTRA_LAZY")
     */
    private $groupUser;

    /**
     * Open flag
     * true = open
     * false = close
     *
     * @ORM\Column(type="boolean")
     */
    private $open;

    /**
     * Threads in group
     *
     * @ORM\OneToMany(targetEntity=Thread::class, mappedBy="group_id", orphanRemoval=true)
     */
    private $threads;

    /**
     * Likes of threads in group
     *
     * @ORM\OneToMany(targetEntity=ThreadUser::class, mappedBy="group_list", orphanRemoval=true)
     */
    private $threadUsers;

    /**
     * Likes of posts in group
     *
     * @ORM\OneToMany(targetEntity=PostUser::class, mappedBy="group_list", orphanRemoval=true)
     */
    private $postUsers;

    /**
     * Group constructor.
     */
    public function __construct()
    {
        $this->threads = new ArrayCollection();
        $this->threadUsers = new ArrayCollection();
        $this->postUsers = new ArrayCollection();
    }

    /**
     * Getter for groupUser
     *
     * @return GroupUser[] users of group
     */
    public function getGroupUser()
    {
        return $this->groupUser;
    }

    /**
     * Getter for id
     * @return int|null id of group
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for name
     *
     * @return string|null name of group
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Setter for name
     *
     * @param string $name new name of group
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Getter for visibility
     *
     * @return bool|null true if visible, false if not
     */
    public function getVisibility(): ?bool
    {
        return $this->visibility;
    }

    /**
     * Setter visibility
     *
     * @param bool $visibility true/false
     * @return $this
     */
    public function setVisibility(bool $visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * Getter for description
     *
     * @return string|null description of group
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Setter for description
     *
     * @param string|null $description new description
     * @return $this
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Getter for date_created
     *
     * @return \DateTimeInterface|null date when group was created
     */
    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->date_created;
    }

    /**
     * Setters for date_created
     *
     * @param \DateTimeInterface $date_created new creation date of group
     * @return $this
     */
    public function setDateCreated(\DateTimeInterface $date_created): self
    {
        $this->date_created = $date_created;

        return $this;
    }

    /**
     * Getter for picture
     *
     * @return string|null name of group picture file
     */
    public function getPicture(): ?string
    {
        return $this->picture;
    }

    /**
     * Setter for picture
     *
     * @param string|null $picture name of new group picture file
     * @return $this
     */
    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Getter for owner of group
     *
     * @return User|null owner of group
     */
    public function getAdminUser(): ?User
    {
        return $this->admin_user;
    }

    /**
     * Setter for owner of group
     *
     * @param User|null $admin_user new owner
     * @return $this
     */
    public function setAdminUser(?User $admin_user): self
    {
        $this->admin_user = $admin_user;

        return $this;
    }

    /**
     * Getter for date_creation in string format
     *
     * @return string datetime of creation of group
     */
    public function getDateString(): string
    {
        return $this->getDateCreated()->format('d/m/Y');
    }

    /**
     * Returns number of members in group
     *
     * @return int number of members in group
     */
    public function getUsersCount(): int
    {
        return count($this->getUsers());
    }

    /**
     * Returns number of moderators in group
     *
     * @return int number of moderators in group
     */
    public function getModsCount(): int
    {
        return count($this->getMods());
    }

    /**
     * Getters for threads in group
     *
     * @return Collection|Thread[] threads in group
     */
    public function getThreads(): Collection
    {
        return $this->threads;
    }

    /**
     * Returns number of threads in group
     *
     * @return int number of threads in group
     */
    public function getThreadsCount(): int
    {
        return count($this->getThreads());
    }

    /**
     * Adds thread to group threads
     *
     * @param Thread $thread new thread to be added
     * @return $this
     */
    public function addThread(Thread $thread): self
    {
        if (!$this->threads->contains($thread)) {
            $this->threads[] = $thread;
            $thread->setGroupId($this);
        }

        return $this;
    }

    /**
     * Removes thread from group
     *
     * @param Thread $thread thread to be removed
     * @return $this
     */
    public function removeThread(Thread $thread): self
    {
        if ($this->threads->removeElement($thread)) {
            // set the owning side to null (unless already changed)
            if ($thread->getGroupId() === $this) {
                $thread->setGroupId(null);
            }
        }

        return $this;
    }

    /**
     * Getter for open
     *
     * @return bool|null true if open, false if not
     */
    public function getOpen(): ?bool
    {
        return $this->open;
    }

    /**
     * Setter for open
     *
     * @param bool $open new open value
     * @return $this
     */
    public function setOpen(bool $open): self
    {
        $this->open = $open;

        return $this;
    }

    /**
     * Checks whether user submitted application to join group
     *
     * @param User|null $user user which is to be checked
     * @return bool true if applied, false otherwise
     */
    public function userApplied(User $user = null)
    {
        if ($user == null){
            return false;
        }
        $appliedUsers = $this->getAppliedUsers();
        return in_array($user, $appliedUsers);
    }

    /**
     * Checks whether user submitted application to join become group moderator
     *
     * @param User|null $user user which is to be checked
     * @return bool true if applied, false otherwise
     */
    public function isAppliedMod(User $user = null)
    {
        if ($user == null){
            return false;
        }
        $appliedMods = $this->getAppliedMods();
        return in_array($user, $appliedMods);
    }

    /**
     * Returns users who submitted application to become members
     *
     * @return User[] users who submitted application to become members
     */
    public function getAppliedUsers()
    {
        $arr = [];
        foreach ($this->getGroupUser() as &$gu){
            if(in_array('ROLE_APP', $gu->getRole())){
                array_push($arr, $gu->getUser());
            }
        }
        return $arr;
    }

    /**
     * Returns users who submitted application to become moderators of the groups
     *
     * @return User[] users who submitted application to become moderators of the groups
     */
    public function getAppliedMods()
    {
        $arr = [];
        foreach ($this->getGroupUser() as &$gu){
            if(in_array('ROLE_MAPP', $gu->getRole())){
                array_push($arr, $gu->getUser());
            }
        }
        return $arr;
    }

    /**
     * Returns moderators of the groups
     *
     * @return User[] moderators of the groups
     */
    public function getMods()
    {
        $arr = [];
        foreach ($this->getGroupUser() as &$gu){
            if(in_array('ROLE_MOD', $gu->getRole())){
                array_push($arr, $gu->getUser());
            }
        }
        return $arr;
    }

    /**
     * Checks whether user is moderator in the group
     *
     * @param User|null $user user
     * @return bool true if mod, false if not
     */
    public function isMod(User $user = null)
    {
        if ($user == null){
            return false;
        }
        $mods = $this->getMods();
        return in_array($user, $mods);
    }

    /**
     * Checks whether user is member in the group
     *
     * @param User|null $user user
     * @return bool true if member, false otherwise
     */
    public function isMember(User $user = null)
    {
        if ($user == null){
            return false;
        }
        $users = $this->getUsers();
        return in_array($user, $users);
    }

    /**
     * Returns members of group
     *
     * @return User[] members of group
     */
    public function getUsers()
    {
        $arr = [];
        foreach ($this->getGroupUser() as $gu){
            if(in_array('ROLE_MEM', $gu->getRole())){
                array_push($arr, $gu->getUser());
            }
        }
        return $arr;
    }

    /**
     * Return members of group who are not moderators
     *
     * @return User[] members of group who are not moderators
     */
    public function getOtherUsers()
    {
        $arr = [];
        foreach ($this->getGroupUser() as $gu){
            if(!in_array('ROLE_MOD', $gu->getRole()) and !in_array('ROLE_APP', $gu->getRole())){
                array_push($arr, $gu->getUser());
            }
        }
        return $arr;
    }

    /**
     * Return likes groups threads
     *
     * @return Collection|ThreadUser[] threads likes
     */
    public function getThreadUsers(): Collection
    {
        return $this->threadUsers;
    }

    /**
     * Adds thread like
     *
     * @param ThreadUser $threadUser thread like
     * @return $this
     */
    public function addThreadUser(ThreadUser $threadUser): self
    {
        if (!$this->threadUsers->contains($threadUser)) {
            $this->threadUsers[] = $threadUser;
            $threadUser->setGroupList($this);
        }

        return $this;
    }

    /**
     * Removes thread like
     *
     * @param ThreadUser $threadUser thread like
     * @return $this
     */
    public function removeThreadUser(ThreadUser $threadUser): self
    {
        if ($this->threadUsers->removeElement($threadUser)) {
            // set the owning side to null (unless already changed)
            if ($threadUser->getGroupList() === $this) {
                $threadUser->setGroupList(null);
            }
        }

        return $this;
    }

    /**
     * Return likes of groups posts
     *
     * @return Collection|PostUser[] likes of groups posts
     */
    public function getPostUsers(): Collection
    {
        return $this->postUsers;
    }

    /**
     * Adds post like
     *
     * @param PostUser $postUser post like
     * @return $this
     */
    public function addPostUser(PostUser $postUser): self
    {
        if (!$this->postUsers->contains($postUser)) {
            $this->postUsers[] = $postUser;
            $postUser->setGroupList($this);
        }

        return $this;
    }

    /**
     * Removes post like
     *
     * @param PostUser $postUser post like
     * @return $this
     */
    public function removePostUser(PostUser $postUser): self
    {
        if ($this->postUsers->removeElement($postUser)) {
            // set the owning side to null (unless already changed)
            if ($postUser->getGroupList() === $this) {
                $postUser->setGroupList(null);
            }
        }

        return $this;
    }
}
