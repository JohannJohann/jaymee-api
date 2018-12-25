<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 */
class Image
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    public $number;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $filename;

    /**
     * @ORM\Column(type="integer")
     */
    public $cost;

    /**
     * @ORM\Column(type="datetime")
     */
    public $created_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="ownImages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User")
     * @ORM\JoinTable(name="image_viewed_by")
     * @ORM\JoinColumn(nullable=false)
     */
    public $viewedBy;

    public function __construct()
    {
        $this->viewedBy = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getCost(): ?int
    {
        return $this->cost;
    }

    public function setCost(int $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getViewedBy(): Collection
    {
        return $this->viewedBy;
    }

    public function addViewedBy(User $viewedBy): self
    {
        if (!$this->viewedBy->contains($viewedBy)) {
            $this->viewedBy[] = $viewedBy;
        }

        return $this;
    }

    public function removeViewedBy(User $viewedBy): self
    {
        if ($this->viewedBy->contains($viewedBy)) {
            $this->viewedBy->removeElement($viewedBy);
        }

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->index;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }
}
