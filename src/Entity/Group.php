<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GroupRepository::class)
 * @ORM\Table(name="`group`")
 */
class Group
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $visibility;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_created;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="admin_for_groups")
     * @ORM\JoinColumn(nullable=false)
     */
    private $admin_user;

    /**
     * @ORM\OneToMany(targetEntity=GroupUser::class, mappedBy="group", fetch="EXTRA_LAZY")
     */
    private $groupUser;

    /**
     * @ORM\Column(type="boolean")
     */
    private $open;

    /**
     * @ORM\OneToMany(targetEntity=Thread::class, mappedBy="group_id", orphanRemoval=true)
     */
    private $threads;

    /**
     * @ORM\OneToMany(targetEntity=ThreadUser::class, mappedBy="group_list", orphanRemoval=true)
     */
    private $threadUsers;

    public function __construct()
    {
        $this->threads = new ArrayCollection();
        $this->threadUsers = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getGroupUser()
    {
        return $this->groupUser;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getVisibility(): ?bool
    {
        return $this->visibility;
    }

    public function setVisibility(bool $visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->date_created;
    }

    public function setDateCreated(\DateTimeInterface $date_created): self
    {
        $this->date_created = $date_created;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getAdminUser(): ?User
    {
        return $this->admin_user;
    }

    public function setAdminUser(?User $admin_user): self
    {
        $this->admin_user = $admin_user;

        return $this;
    }

    public function getDateString(): string
    {
        return $this->getDateCreated()->format('d/m/Y');
    }

    public function getUsersCount(): int
    {
        return count($this->getUsers());
    }

    /**
     * @return Collection|Thread[]
     */
    public function getThreads(): Collection
    {
        return $this->threads;
    }

    public function addThread(Thread $thread): self
    {
        if (!$this->threads->contains($thread)) {
            $this->threads[] = $thread;
            $thread->setGroupId($this);
        }

        return $this;
    }

    public function removeThreah(Thread $thread): self
    {
        if ($this->threads->removeElement($thread)) {
            // set the owning side to null (unless already changed)
            if ($thread->getGroupId() === $this) {
                $thread->setGroupId(null);
            }
        }

        return $this;
    }

    public function getOpen(): ?bool
    {
        return $this->open;
    }

    public function setOpen(bool $open): self
    {
        $this->open = $open;

        return $this;
    }

    public function isUserInGroup(User $user)
    {
        $users = $this->getUsers();
        return in_array($user, $users);
    }

    public function userApplied(User $user = null)
    {
        if ($user == null){
            return false;
        }
        $appliedUsers = $this->getAppliedUsers();
        return in_array($user, $appliedUsers);
    }

    public function isAppliedMod(User $user = null)
    {
        if ($user == null){
            return false;
        }
        $appliedMods = $this->getAppliedMods();
        return in_array($user, $appliedMods);
    }

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

    public function isMod(User $user = null)
    {
        if ($user == null){
            return false;
        }
        $mods = $this->getMods();
        return in_array($user, $mods);
    }

    public function isMember(User $user = null)
    {
        if ($user == null){
            return false;
        }
        $users = $this->getUsers();
        return in_array($user, $users);
    }

    public function getUsers()
    {
        $arr = [];
        foreach ($this->getGroupUser() as &$gu){
            if(in_array('ROLE_MEM', $gu->getRole())){
                array_push($arr, $gu->getUser());
            }
        }
        return $arr;
    }

    /**
     * @return Collection|ThreadUser[]
     */
    public function getThreadUsers(): Collection
    {
        return $this->threadUsers;
    }

    public function addThreadUser(ThreadUser $threadUser): self
    {
        if (!$this->threadUsers->contains($threadUser)) {
            $this->threadUsers[] = $threadUser;
            $threadUser->setGroupList($this);
        }

        return $this;
    }

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
}
