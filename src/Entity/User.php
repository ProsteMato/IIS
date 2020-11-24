<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
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
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=30)
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
     * @ORM\Column(type="boolean")
     */
    private $visibility;
    
    /**
     * @ORM\OneToMany(targetEntity=Group::class, mappedBy="admin_user")
     */
    private $admin_for_groups;

    /**
     * @ORM\ManyToMany(targetEntity=Group::class, mappedBy="users")
     */
    private $liked_groups;

    public function __construct()
    {
        $this->admin_for_groups = new ArrayCollection();
        $this->liked_groups = new ArrayCollection();
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

    public function getVisibility(): ?bool
    {
        return $this->visibility;
    }

    public function setVisibility(bool $visibility): self
    {
        $this->visibility = $visibility;

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
}
