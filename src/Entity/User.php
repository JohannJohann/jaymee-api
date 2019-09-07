<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

use App\Entity\User;
use App\Entity\MediaKey;

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
     * @ORM\OneToMany(targetEntity="App\Entity\MediaKey", mappedBy="owner")
     * @ORM\JoinColumn(nullable=false)
     */
    private $unlockedKeys;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MediaKey", mappedBy="target")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sharedKeys;

       /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="followedBy")
     * @ORM\JoinTable(name="user_follows")
     */
    private $following;

      /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="following")
     * @ORM\JoinTable(name="user_follows")
     */
    public $followedBy;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $last_activity_at;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fcm_token;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    public $has_privileges;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="blockedBy")
     * @ORM\JoinTable(name="user_blocks")
     */
    private $blocking;

      /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="blocking")
     * @ORM\JoinTable(name="user_blocks")
     */
    public $blockedby;

    public function __construct()
    {
        $this->ownQuizzes = new ArrayCollection();
        $this->ownImages = new ArrayCollection();
        $this->unlockedKeys = new ArrayCollection();
        $this->sharedKeys = new ArrayCollection();
        $this->following = new ArrayCollection();
        $this->followedBy = new ArrayCollection();
        $this->blocking = new ArrayCollection();
        $this->blockedby = new ArrayCollection();
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
     * @return Collection|MediaKey[]
     */
    public function getUnlockedKeys(): Collection
    {
        return $this->unlockedKeys;
    }

    public function addUnlockedKey(MediaKey $unlockedKey): self
    {
        if (!$this->unlockedKeys->contains($unlockedKey)) {
            $this->unlockedKeys[] = $unlockedKey;
            $unlockedKey->setOwner($this);
        }

        return $this;
    }

    public function removeUnlockedKey(MediaKey $unlockedKey): self
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
     * @return Collection|MediaKey[]
     */
    public function getSharedKeys(): Collection
    {
        return $this->sharedKeys;
    }

    /**
     * @return int
     */
    public function countSharedKeysFor(User $user): int
    {
        if(!$this->sharedKeys->isEmpty()){
            $matchingKey =  $this->sharedKeys->filter(function(MediaKey $key) use ($user) {
                        return $key->getOwner() == $user;
                    });
            if(!$matchingKey->isEmpty()){
                return $matchingKey->first()->getQuantity();
            } else {
                return 0;
            }
        }
        else {
            return 0 ;
        }
    }

    /**
     * @return MediaKey
     */
    public function getSharedKeysFor(User $user): MediaKey
    {
        if(!$this->sharedKeys->isEmpty()){
            $matchingKey =  $this->sharedKeys->filter(function(MediaKey $key) use ($user) {
                        return $key->getOwner() == $user;
                    });
            if(!$matchingKey->isEmpty()){
                return $matchingKey->first();
            } else {
                return null;
            }
        }
        else {
            return null;
        }
    }

    public function addSharedKey(MediaKey $sharedKey): self
    {
        if (!$this->sharedKeys->contains($sharedKey)) {
            $this->sharedKeys[] = $sharedKey;
            $sharedKey->setTarget($this);
        }

        return $this;
    }

    public function removeSharedKey(MediaKey $sharedKey): self
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

    public function getLastActivityAt(): ?\DateTimeInterface
    {
        return $this->last_activity_at;
    }

    public function setLastActivityAt(?\DateTimeInterface $last_activity_at): self
    {
        $this->last_activity_at = $last_activity_at;

        return $this;
    }

    public function getFcmToken(): ?string
    {
        return $this->fcm_token;
    }

    public function setFcmToken(?string $fcm_token): self
    {
        $this->fcm_token = $fcm_token;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getFollowedBy(): Collection
    {
        return $this->followedBy;
    }

    public function addFollowedBy(User $followedBy): self
    {
        if (!$this->followedBy->contains($followedBy)) {
            $this->followedBy[] = $followedBy;
            $followedBy->addFollowing($this);
        }

        return $this;
    }

    public function removeFollowedBy(User $followedBy): self
    {
        if ($this->followedBy->contains($followedBy)) {
            $this->followedBy->removeElement($followedBy);
            $followedBy->removeFollowing($this);
        }

        return $this;
    }

    public function getHasPrivileges(): ?bool
    {
        return $this->has_privileges;
    }

    public function setHasPrivileges(?bool $has_privileges): self
    {
        $this->has_privileges = $has_privileges;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getBlocking(): Collection
    {
        return $this->blocking;
    }

    public function addBlocking(User $blocking): self
    {
        if (!$this->blocking->contains($blocking)) {
            $this->blocking[] = $blocking;
        }

        return $this;
    }

    public function removeBlocking(User $blocking): self
    {
        if ($this->blocking->contains($blocking)) {
            $this->blocking->removeElement($blocking);
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getBlockedby(): Collection
    {
        return $this->blockedby;
    }

    public function addBlockedby(User $blockedby): self
    {
        if (!$this->blockedby->contains($blockedby)) {
            $this->blockedby[] = $blockedby;
            $blockedby->addBlocking($this);
        }

        return $this;
    }

    public function removeBlockedby(User $blockedby): self
    {
        if ($this->blockedby->contains($blockedby)) {
            $this->blockedby->removeElement($blockedby);
            $blockedby->removeBlocking($this);
        }

        return $this;
    }

    public function isBlocking(User $user): bool {
        return $this->blocking->contains($user);
    }

    public function isBlockedBy(User $user): bool {
        return $this->blockedBy->contains($user);
    }
}
