<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 *
 * Class representing registered user
 *
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 * fields={"email"},
 * errorPath="email",
 * message="It appears you have already registered with this email."
 *)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.")
     * @Assert\NotNull
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * * @Assert\Length(
     *     min = 6,
     *     minMessage = "Password must be at least 6 characters long!")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotNull
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotNull
     */
    private $lastName;

    /**
     * @ORM\Column(type="date",  nullable=true)
     */
    private $birthDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $profilePicture;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=6, nullable=true)
     *
     */
    private $sex;


    /**
     * @ORM\OneToMany(targetEntity=Group::class, mappedBy="admin_user")
     */
    private $admin_for_groups;

    /**
     * @ORM\OneToMany(targetEntity=Thread::class, mappedBy="created_by")
     */
    private $threads;

    /**
     * @ORM\OneToMany(targetEntity=GroupUser::class, mappedBy="user")
     */
    private $groupUser;

    /**
     * @return mixed
     */
    public function getGroupUser()
    {
        return $this->groupUser;
    }

    /**
     * @param mixed $groupUser
     */
    public function setGroupUser($groupUser): void
    {
        $this->groupUser = $groupUser;
    }

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="created_by")
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity=ThreadUser::class, mappedBy="users")
     */
    private $threadUsers;

    /**
     * @ORM\OneToMany(targetEntity=PostUser::class, mappedBy="users")
     */
    private $postUsers;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="subscribedBy")
     */
    private $subscribers;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="subscribers")
     */
    private $subscribedBy;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $visibility;

    public function __construct()
    {
        $this->admin_for_groups = new ArrayCollection();
        $this->threads = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->threadUsers = new ArrayCollection();
        $this->postUsers = new ArrayCollection();
        $this->subscribers = new ArrayCollection();
        $this->subscribedBy = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate ): self
    {

        $this->birthDate = $birthDate;

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): self
    {
        $this->profilePicture = $profilePicture;

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

    public function getSex(): ?string
    {
        return $this->sex;
    }

    public function setSex(string $sex): self
    {
        $this->sex = $sex;

        return $this;
    }


    /**
     * @return Collection|Group[]
     */
    public function getAdminForGroups(): Collection
    {
        return $this->admin_for_groups;
    }

    public function addAdminForGroup(Group $adminForGroup): self
    {
        if (!$this->admin_for_groups->contains($adminForGroup)) {
            $this->admin_for_groups[] = $adminForGroup;
            $adminForGroup->setAdminUser($this);
        }

        return $this;
    }

    public function removeAdminForGroup(Group $adminForGroup): self
    {
        if ($this->admin_for_groups->removeElement($adminForGroup)) {
            // set the owning side to null (unless already changed)
            if ($adminForGroup->getAdminUser() === $this) {
                $adminForGroup->setAdminUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Group[]
     */
    public function getLikedGroups(): Collection
    {
        return $this->liked_groups;
    }

    public function addLikedGroup(Group $likedGroup): self
    {
        if (!$this->liked_groups->contains($likedGroup)) {
            $this->liked_groups[] = $likedGroup;
            $likedGroup->addUser($this);
        }

        return $this;
    }

    public function removeLikedGroup(Group $likedGroup): self
    {
        if ($this->liked_groups->removeElement($likedGroup)) {
            $likedGroup->removeUser($this);
        }

        return $this;
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
            $thread->setCreatedBy($this);
        }

        return $this;
    }

    public function removeThread(Thread $thread): self
    {
        if ($this->threads->removeElement($thread)) {
            // set the owning side to null (unless already changed)
            if ($thread->getCreatedBy() === $this) {
                $thread->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function deletePosts(Post $post) {
        $this->posts->remove($post);
    }

    public function deleteThread(Thread $thread) {
        $this->threads->remove($thread);
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setCreatedBy($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getCreatedBy() === $this) {
                $post->setCreatedBy(null);
            }
        }

        return $this;
    }

    public function getGroups()
    {
        $arr = [];
        foreach ($this->groupUser as &$gu){
            if (in_array('ROLE_MEM', $gu->getRole())){
                array_push($arr, $gu->getGroup());
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
            $threadUser->setUsers($this);
        }

        return $this;
    }

    public function removeThreadUser(ThreadUser $threadUser): self
    {
        if ($this->threadUsers->removeElement($threadUser)) {
            // set the owning side to null (unless already changed)
            if ($threadUser->getUsers() === $this) {
                $threadUser->setUsers(null);
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
            $postUser->setUsers($this);
        }

        return $this;
    }

    public function removePostUser(PostUser $postUser): self
    {
        if ($this->postUsers->removeElement($postUser)) {
            // set the owning side to null (unless already changed)
            if ($postUser->getUsers() === $this) {
                $postUser->setUsers(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getSubscribers(): Collection
    {
        return $this->subscribers;
    }

    public function isFollowing($user)
    {
        return $this->subscribers->contains($user);
    }

    public function addSubscriber(self $subscriber): self
    {
        if (!$this->subscribers->contains($subscriber)) {
            $this->subscribers[] = $subscriber;
        }

        return $this;
    }

    public function removeSubscriber(self $subscriber): self
    {
        $this->subscribers->removeElement($subscriber);

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getSubscribedBy(): Collection
    {
        return $this->subscribedBy;
    }

    public function addSubscribedBy(self $subscribedBy): self
    {
        if (!$this->subscribedBy->contains($subscribedBy)) {
            $this->subscribedBy[] = $subscribedBy;
            $subscribedBy->addSubscriber($this);
        }

        return $this;
    }

    public function removeSubscribedBy(self $subscribedBy): self
    {
        if ($this->subscribedBy->removeElement($subscribedBy)) {
            $subscribedBy->removeSubscriber($this);
        }

        return $this;
    }

    public function getVisibility(): ?string
    {
        return $this->visibility;
    }

    public function setVisibility(string $visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload){
        if ($this->getFirstName() == null or $this->getFirstName() == "")
        {
            $context->buildViolation('First name should not be empty!')
                ->atPath('firstName')
                ->addViolation();
        }

        if ($this->getLastName() == null or $this->getLastName() == "")
        {
            $context->buildViolation('Last name should not be empty!')
                ->atPath('lastName')
                ->addViolation();
        }

        if ($this->getEmail() == null or $this->getEmail() == "")
        {
            $context->buildViolation('Email should not be empty!')
                ->atPath('email')
                ->addViolation();
        }
    }

}
