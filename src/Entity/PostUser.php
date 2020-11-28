<?php

namespace App\Entity;

use App\Repository\PostUserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostUserRepository::class)
 */
class PostUser
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
     * @ORM\ManyToOne(targetEntity=Thread::class, inversedBy="postUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $threads;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class, inversedBy="postUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $group_list;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="postUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $posts;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="postUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $users;

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

    public function getGroupList(): ?Group
    {
        return $this->group_list;
    }

    public function setGroupList(?Group $group_list): self
    {
        $this->group_list = $group_list;

        return $this;
    }

    public function getPosts(): ?Post
    {
        return $this->posts;
    }

    public function setPosts(?Post $posts): self
    {
        $this->posts = $posts;

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
}
