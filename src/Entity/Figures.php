<?php

namespace App\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\FiguresRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: FiguresRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'Cette figure existe déjà')]
class Figures
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique:true)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creation_date = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $update_date = null;

    #[ORM\ManyToOne(targetEntity:Users::class, inversedBy:'figures')]
    #[ORM\JoinColumn(name:'users_id',nullable: false)]
    private ?Users $users_id = null;

    #[ORM\ManyToOne(targetEntity:Groups::class)]
    #[ORM\JoinColumn(name:'groups_id',nullable: false)]
    private ?Groups $groups_id = null;

    #[ORM\OneToMany(targetEntity:Videos::class, mappedBy:'figures_id')]
    private Collection $videos;

    #[ORM\ManyToMany(targetEntity: Images::class,cascade:["persist"])]
    private Collection $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->creation_date = new DateTime();
        $this->update_date = null;
    }

    public function getVideos(): Collection
    {
        return $this->videos;
    }


    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creation_date;
    }

    public function setCreationDate(\DateTimeInterface $creation_date = new DateTime()): static
    {
        $this->creation_date = $creation_date;

        return $this;
    }

    public function getUpdateDate(): ?\DateTimeInterface
    {
        return $this->update_date;
    }

    public function setUpdateDate(?\DateTimeInterface $update_date): static
    {
        $this->update_date = $update_date;

        return $this;
    }

    public function getUsersId(): ?Users
    {
        return $this->users_id;
    }

    public function setUsersId(?Users $users_id): static
    {
        $this->users_id = $users_id;

        return $this;
    }

    public function getGroupsId(): ?Groups
    {
        return $this->groups_id;
    }

    public function setGroupsId(?Groups $groups_id): static
    {
        $this->groups_id = $groups_id;

        return $this;
    }

    /**
     * @return Collection<int, images>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
        }

        return $this;
    }

    public function removeImage(Images $image): static
    {
        $this->images->removeElement($image);

        return $this;
    }
}
