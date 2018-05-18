<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @ORM\HasLifecycleCallbacks()
 */
class User{

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="sender")
     */
    private $outgoingMessages;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="addressee")
     */
    private $incomingMessages;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    public function __construct()
    {
        $this->incomingMessages = new ArrayCollection();
        $this->outgoingMessages = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return User
     */
    public function setId(int $id): User
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return User
     */
    public function setName(string $name): User
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return string|UploadedFile|null
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param string|UploadedFile $avatar
     * @return User
     */
    public function setAvatar($avatar): User
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getIncomingMessages(): Collection
    {
        return $this->incomingMessages;
    }

    /**
     * @return Collection|Message[]
     */
    public function getOutgoingMessages(): Collection
    {
        return $this->outgoingMessages;
    }

    /**
     * @param Message $message
     * @return User
     */
    public function addIncomingMessage(Message $message): User
    {
        if(!$this->incomingMessages->contains($message)) {
            $this->incomingMessages->add($message);
        }

        return $this;
    }

    /**
     * @param Message $message
     * @return User
     */
    public function addOutgoingMessage(Message $message): User
    {
        if(!$this->outgoingMessages->contains($message)) {
            $this->outgoingMessages->add($message);
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return User
     */
    public function setToken(string $token): User
    {
        $this->token = $token;

        return $this;
    }
}
