<?php

namespace App\Entity;


use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\MessagesRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MessagesRepository::class)]
class Messages
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(
        min: 5,
        max: 5000,
        minMessage: 'Ce commentaire est trop court, min = {{ limit }} caractères',
        maxMessage: 'Ce commentaire est trop long, max = {{ limit }} caractères',
    )]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $message_date = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?figures $figures = null;

    #[ORM\ManyToOne(inversedBy: 'messages', targetEntity:Users::class)]
    #[ORM\JoinColumn(nullable: false, name:'users_id')]
    private ?users $users = null;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getContent(): ?string
    {
        return $this->content;
    }


    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }


    public function getMessageDate(): ?\DateTimeInterface
    {
        return $this->message_date;
    }


    public function setMessageDate(\DateTimeInterface $message_date = new DateTime() ): static
    {
        $this->message_date = $message_date;

        return $this;
    }


    public function getFigures(): ?figures
    {
        return $this->figures;
    }


    public function setFigures(?figures $figures): static
    {
        $this->figures = $figures;

        return $this;
    }


    public function getUsers(): ?users
    {
        return $this->users;
    }


    public function setUsers(?users $users): static
    {
        $this->users = $users;

        return $this;
    }


}