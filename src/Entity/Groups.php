<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\GroupsRepository;

#[ORM\Entity(repositoryClass: GroupsRepository::class)]
#[ORM\Table(name: '`groups`')]
class Groups
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    private ?string $group_name = null;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getGroupName(): ?string
    {
        return $this->group_name;
    }


    public function setGroupName(string $group_name): static
    {
        $this->group_name = $group_name;

        return $this;
    }


}
