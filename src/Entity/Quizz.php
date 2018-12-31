<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuizzRepository")
 */
class Quizz
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $question;

    /**
     * @ORM\Column(type="array")
     */
    public $choices = [];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $correct_answer;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="integer")
     */
    public $attempts;

    /**
     * @ORM\Column(type="integer")
     */
    public $successes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="ownQuizzes", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    public $owner;
    
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User")
     * @ORM\JoinTable(name="quizz_answered_by")
     */
    private $answeredBy;

    public function __construct()
    {
        $this->answeredBy = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getChoices(): ?array
    {
        return $this->choices;
    }

    public function setChoices(array $choices): self
    {
        $this->choices = $choices;

        return $this;
    }

    public function getCorrectAnswer(): ?string
    {
        return $this->correct_answer;
    }

    public function setCorrectAnswer(string $correct_answer): self
    {
        $this->correct_answer = $correct_answer;

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

    public function getAttempts(): ?int
    {
        return $this->attempts;
    }

    public function setAttempts(int $attempts): self
    {
        $this->attempts = $attempts;

        return $this;
    }

    public function getSuccesses(): ?int
    {
        return $this->successes;
    }

    public function setSuccesses(int $successes): self
    {
        $this->successes = $successes;

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
    public function getAnsweredBy(): Collection
    {
        return $this->answeredBy;
    }

    public function addAnsweredBy(User $answeredBy): self
    {
        if (!$this->answeredBy->contains($answeredBy)) {
            $this->answeredBy[] = $answeredBy;
        }

        return $this;
    }

    public function removeAnsweredBy(User $answeredBy): self
    {
        if ($this->answeredBy->contains($answeredBy)) {
            $this->answeredBy->removeElement($answeredBy);
        }

        return $this;
    }
}
