<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UsersRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['name'], message: 'There is already an account with this name')]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 10,
        max: 180,
        minMessage: 'Votre adresse {{ value }}, est trop courte, min = {{ limit }} caractères',
        maxMessage: 'Votre adresse est trop longue',
    )]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank(['message' => 'Mots de passe vide'])]
    #[Assert\Length(
        min: 6,
        minMessage: 'Votre mot de passe doit faire {{ limit }} caractères minimum'
    )]
    #[Assert\Type('string')]
    private ?string $password = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: '{{ value }} est trop court. Votre pseudo doit faire {{ limit }} caractères minimum'
    )]
    private ?string $name = null;

    #[ORM\Column(length: 100,options:['default' => "snowboarder-310459.png"])]
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 5,
        max: 100,
        minMessage: 'Le nom {{ value }} pour votre photo est trop court. {{ limit }} caractères minimum',
        maxMessage: 'Le nom {{ value }} pour votre photo est trop long. {{ limit }} caractères maximum'
    )]
    private ?string $photo = "snowboarder-310459.png";

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $confirmationDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $sendLink = null;

    #[ORM\Column]
    #[Assert\Type('string')]
    private ?string $token = null;

    #[ORM\OneToMany(targetEntity:Figures::class, mappedBy:'users_id')]
    private $figures;
    
    public function __construct()
    {
        $this->figures = new ArrayCollection();
    }

    public function getFigures() :Collection
    {
        return $this->figures;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
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

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo = "snowboarder-310459.png"): static
    {
        $this->photo = $photo;

        return $this;
    }


    public function getConfirmationDate(): ?\DateTimeInterface
    {
        return $this->confirmationDate;
    }

    public function setConfirmationDate(?\DateTimeInterface $confirmationDate): static
    {
        $this->confirmationDate = $confirmationDate;

        return $this;
    }


    public function getSendLink(): ?\DateTimeInterface
    {
        return $this->sendLink;
    }

    public function setSendLink(?\DateTimeInterface $sendLink): static
    {
        $this->sendLink = $sendLink;

        return $this;
    }


    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): static
    {
        $this->token = $token;

        return $this;
    }
}
