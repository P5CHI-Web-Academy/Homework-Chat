<?php
namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="message")
 * @ORM\HasLifecycleCallbacks()
 */
class Message{

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $messageText;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="outgoingMessages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sender;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="incomingMessages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $addressee;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $seen;

    /**
     * @var datetime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Message
     */
    public function setId(int $id): Message
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessageText(): string
    {
        return $this->messageText;
    }

    /**
     * @param string $messageText
     * @return Message
     */
    public function setMessageText(string $messageText): Message
    {
        $this->messageText = $messageText;

        return $this;
    }

    /**
     * @return User
     */
    public function getSender(): User
    {
        return $this->sender;
    }

    /**
     * @param User $sender
     * @return Message
     */
    public function setSender(User $sender): Message
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * @return User
     */
    public function getAddressee(): User
    {
        return $this->addressee;
    }

    /**
     * @param User $addressee
     * @return Message
     */
    public function setAddressee(User $addressee): Message
    {
        $this->addressee = $addressee;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist(): void
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return bool
     */
    public function isSeen(): bool
    {
        return $this->seen;
    }

    /**
     * @param bool $seen
     * @return Message
     */
    public function setSeen(bool $seen): Message
    {
        $this->seen = $seen;

        return $this;
    }
}
