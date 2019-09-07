<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FlagRepository")
 */
class Flag
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
    * @ORM\ManyToOne(targetEntity="App\Entity\User")
    * @ORM\JoinColumn(nullable=false)
    */
    private $flaggedBy;

    /**
    * @ORM\ManyToOne(targetEntity="App\Entity\Image")
    * @ORM\JoinColumn(nullable=true)
    */
    private $flaggedContent;

    /**
    * @ORM\ManyToOne(targetEntity="App\Entity\User")
    * @ORM\JoinColumn(nullable=false)
    */
    private $flaggedUser;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFlaggedBy(): ?User
    {
        return $this->flaggedBy;
    }

    public function setFlaggedBy(?User $flaggedBy): self
    {
        $this->flaggedBy = $flaggedBy;

        return $this;
    }

    public function getFlaggedContent(): ?Image
    {
        return $this->flaggedContent;
    }

    public function setFlaggedContent(?Image $flaggedContent): self
    {
        $this->flaggedContent = $flaggedContent;

        return $this;
    }

    public function getFlaggedUser(): ?User
    {
        return $this->flaggedUser;
    }

    public function setFlaggedUser(?User $flaggedUser): self
    {
        $this->flaggedUser = $flaggedUser;

        return $this;
    }
}
