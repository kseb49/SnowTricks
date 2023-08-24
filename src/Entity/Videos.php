<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\VideosRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: VideosRepository::class)]
#[UniqueEntity(fields: ['src'], message: 'Cette vidéo est déjà utilisée')]
class Videos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank]
    private ?string $src = null;

    #[ORM\ManyToOne(targetEntity:Figures::class, inversedBy:'videos')]
    #[ORM\JoinColumn(name: 'figures_id')]
    private ?figures $figures_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSrc(): ?string
    {
        return $this->src;
    }

    public function setSrc(string $src): static
    {
        $this->src = $src;

        return $this;
    }

    public function getFiguresId(): ?figures
    {
        return $this->figures_id;
    }

    public function setFiguresId(?figures $figures_id): static
    {
        $this->figures_id = $figures_id;

        return $this;
    }
}
