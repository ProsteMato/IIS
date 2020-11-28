<?php

namespace App\Entity;

use App\Repository\ThreadUserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ThreadUserRepository::class)
 */
class ThreadUser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $liked;

    /**
     * @ORM\ManyToOne(targetEntity=Thread::class, inversedBy="threadUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $threads;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="threadUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class, inversedBy="threadUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $group_list;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLiked(): ?string
    {
        return $this->liked;
    }

    public function setLiked(string $liked): self
    {
        $this->liked = $liked;

        return $this;
    }

    public function getThreads(): ?Thread
    {
        return $this->threads;
    }

    public function setThreads(?Thread $threads): self
    {
        $this->threads = $threads;

        return $this;
    }

    public function getUsers(): ?User
    {
        return $this->users;
    }

    public function setUsers(?User $users): self
    {
        $this->users = $users;

        return $this;
    }

    public function getGroupList(): ?Group
    {
        return $this->group_list;
    }

    public function setGroupList(?Group $group_list): self
    {
        $this->group_list = $group_list;

        return $this;
    }
}
