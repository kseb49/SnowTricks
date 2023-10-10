<?php

namespace App\Entity;

use App\Repository\ImagesRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImagesRepository::class)]
class Images
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private ?string $image_name = null;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getImageName(): ?string
    {
        return $this->image_name;
    }


    public function setImageName(string $image_name): static
    {
        $this->image_name = $image_name;

        return $this;
    }


}
