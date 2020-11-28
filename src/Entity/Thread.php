<?php

namespace App\Entity;

use App\Repository\ThreadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ThreadRepository::class)
 */
class Thread
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
    private $title;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $rating;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creation_date;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="thread", orphanRemoval=true)
     */
    private $posts;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class, inversedBy="threahs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $group_id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="threahs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $created_by;

    /**
     * @ORM\OneToMany(targetEntity=ThreadUser::class, mappedBy="threads", orphanRemoval=true)
     */
    private $threadUsers;

    /**
     * @ORM\OneToMany(targetEntity=PostUser::class, mappedBy="threads", orphanRemoval=true)
     */
    private $postUsers;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->threadUsers = new ArrayCollection();
        $this->postUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getDateString(): string
    {
        return $this->getCreationDate()->format('d.m.Y H:i:s');
    }

    public function setCreationDate(\DateTimeInterface $creation_date): self
    {
        $this->creation_date = $creation_date;

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function getPostsCount(): int
    {
        return count($this->posts);
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setThread($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getThread() === $this) {
                $post->setThread(null);
            }
        }

        return $this;
    }

    public function getGroupId(): ?Group
    {
        return $this->group_id;
    }

    public function setGroupId(?Group $group_id): self
    {
        $this->group_id = $group_id;

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
            $threadUser->setThreads($this);
        }

        return $this;
    }

    public function removeThreadUser(ThreadUser $threadUser): self
    {
        if ($this->threadUsers->removeElement($threadUser)) {
            // set the owning side to null (unless already changed)
            if ($threadUser->getThreads() === $this) {
                $threadUser->setThreads(null);
            }
        }

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
            $postUser->setThreads($this);
        }

        return $this;
    }

    public function removePostUser(PostUser $postUser): self
    {
        if ($this->postUsers->removeElement($postUser)) {
            // set the owning side to null (unless already changed)
            if ($postUser->getThreads() === $this) {
                $postUser->setThreads(null);
            }
        }

        return $this;
    }
}
