<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

use App\Entity\User;
use App\Entity\Key;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $instagram_token;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $salt;

       /**
     * @ORM\Column(type="array")
     */
    private $roles;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $profile_pic;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Quizz", mappedBy="owner" )
     * @ORM\JoinColumn(nullable=false)
     */
    private $ownQuizzes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="owner")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ownImages;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Key", mappedBy="owner")
     * @ORM\JoinColumn(nullable=false)
     */
    private $unlockedKeys;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Key", mappedBy="target")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sharedKeys;

       /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User")
     * @ORM\JoinTable(name="user_follows")
     */
    private $following;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $token;

    public function __construct()
    {
        $this->ownQuizzes = new ArrayCollection();
        $this->ownImages = new ArrayCollection();
        $this->unlockedKeys = new ArrayCollection();
        $this->sharedKeys = new ArrayCollection();
        $this->following = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInstagramToken(): ?string
    {
        return $this->instagram_token;
    }

    public function setInstagramToken(?string $instagram_token): self
    {
        $this->instagram_token = $instagram_token;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getProfilePic(): ?string
    {
        return $this->profile_pic;
    }

    public function setProfilePic(?string $profile_pic): self
    {
        $this->profile_pic = $profile_pic;

        return $this;
    }

    /**
     * @return Collection|Quizz[]
     */
    public function getOwnQuizzes(): Collection
    {
        return $this->ownQuizzes;
    }

    public function addOwnQuiz(Quizz $ownQuiz): self
    {
        if (!$this->ownQuizzes->contains($ownQuiz)) {
            $this->ownQuizzes[] = $ownQuiz;
            $ownQuiz->setOwner($this);
        }

        return $this;
    }

    public function removeOwnQuiz(Quizz $ownQuiz): self
    {
        if ($this->ownQuizzes->contains($ownQuiz)) {
            $this->ownQuizzes->removeElement($ownQuiz);
            // set the owning side to null (unless already changed)
            if ($ownQuiz->getOwner() === $this) {
                $ownQuiz->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getOwnImages(): Collection
    {
        return $this->ownImages;
    }

    public function addOwnImage(Quizz $ownImage): self
    {
        if (!$this->ownImages->contains($ownImage)) {
            $this->ownImages[] = $ownImage;
            $ownImage->setOwner($this);
        }

        return $this;
    }

    public function removeOwnImage(Quizz $ownImage): self
    {
        if ($this->ownImages->contains($ownImage)) {
            $this->ownImages->removeElement($ownImage);
            // set the owning side to null (unless already changed)
            if ($ownImage->getOwner() === $this) {
                $ownImage->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Key[]
     */
    public function getUnlockedKeys(): Collection
    {
        return $this->unlockedKeys;
    }

    public function addUnlockedKey(Key $unlockedKey): self
    {
        if (!$this->unlockedKeys->contains($unlockedKey)) {
            $this->unlockedKeys[] = $unlockedKey;
            $unlockedKey->setOwner($this);
        }

        return $this;
    }

    public function removeUnlockedKey(Key $unlockedKey): self
    {
        if ($this->unlockedKeys->contains($unlockedKey)) {
            $this->unlockedKeys->removeElement($unlockedKey);
            // set the owning side to null (unless already changed)
            if ($unlockedKey->getOwner() === $this) {
                $unlockedKey->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Key[]
     */
    public function getSharedKeys(): Collection
    {
        return $this->sharedKeys;
    }

    /**
     * @return int
     */
    public function getSharedKeysFor(User $user): int
    {
        if(!$this->getSharedKeys()->isEmpty()){
            return 5;//$this->sharedKeys->get(0)->getQuantity();
        }
        else {
            return 0;
        }//->filter(function(Key $key) use ($user) {
        //     return $key->getTarget() == $user;
        // });
    }

    public function addSharedKey(Key $sharedKey): self
    {
        if (!$this->sharedKeys->contains($sharedKey)) {
            $this->sharedKeys[] = $sharedKey;
            $sharedKey->setTarget($this);
        }

        return $this;
    }

    public function removeSharedKey(Key $sharedKey): self
    {
        if ($this->sharedKeys->contains($sharedKey)) {
            $this->sharedKeys->removeElement($sharedKey);
            // set the owning side to null (unless already changed)
            if ($sharedKey->getTarget() === $this) {
                $sharedKey->setTarget(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getFollowing(): Collection
    {
        return $this->following;
    }

    public function addFollowing(User $following): self
    {
        if (!$this->following->contains($following)) {
            $this->following[] = $following;
        }

        return $this;
    }

    public function removeFollowing(User $following): self
    {
        if ($this->following->contains($following)) {
            $this->following->removeElement($following);
        }

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials() {

    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }
}
