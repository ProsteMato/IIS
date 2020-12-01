<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\Column(type="integer")
     */
    private $rating;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creation_date;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="replays")
     * @ORM\JoinColumn(nullable=true)
     */
    private $post;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="post", orphanRemoval=true)
     */
    private $replays;

    /**
     * @ORM\ManyToOne(targetEntity=Thread::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $thread;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=true)
     */
    private $created_by;

    /**
     * @ORM\OneToMany(targetEntity=PostUser::class, mappedBy="posts")
     */
    private $postUsers;

    public function __construct()
    {
        $this->replays = new ArrayCollection();
        $this->postUsers = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creation_date;
    }

    public function getStringCreationDate(): string
    {
        return $this->creation_date->format("d.m.Y H:i:s");
    }

    public function setCreationDate(\DateTimeInterface $creation_date): self
    {
        $this->creation_date = $creation_date;

        return $this;
    }

    public function getPost(): ?self
    {
        return $this->post;
    }

    public function setPost(?self $post): self
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getReplays(): Collection
    {
        return $this->replays;
    }

    public function addReplay(self $replay): self
    {
        if (!$this->replays->contains($replay)) {
            $this->replays[] = $replay;
            $replay->setPost($this);
        }

        return $this;
    }

    public function removeReplay(self $replay): self
    {
        if ($this->replays->removeElement($replay)) {
            // set the owning side to null (unless already changed)
            if ($replay->getPost() === $this) {
                $replay->setPost(null);
            }
        }

        return $this;
    }

    public function getThread(): ?Thread
    {
        return $this->thread;
    }

    public function setThread(?Thread $thread): self
    {
        $this->thread = $thread;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->created_by;
    }

    public function setCreatedBy(?User $created_by): self
    {
        $this->created_by = $created_by;

        return $this;
    }

    /**
     * @return Collection|PostUser[]
     */
    public function getPostUsers(): Collection
    {
        return $this->postUsers;
    }

    public function addPostUser(PostUser $postUser): self
    {
        if (!$this->postUsers->contains($postUser)) {
            $this->postUsers[] = $postUser;
            $postUser->setPosts($this);
        }

        return $this;
    }

    public function removePostUser(PostUser $postUser): self
    {
        if ($this->postUsers->removeElement($postUser)) {
            // set the owning side to null (unless already changed)
            if ($postUser->getPosts() === $this) {
                $postUser->setPosts(null);
            }
        }

        return $this;
    }
}
