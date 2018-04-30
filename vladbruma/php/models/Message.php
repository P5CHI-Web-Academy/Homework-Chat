<?php
namespace Framework\Models;

use Doctrine\ORM\Mapping as ORM;
use Framework\Traits\CreatedModifiedTrait;
use Framework\Traits\IdentityTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="messages")
 * @ORM\Entity(repositoryClass="Framework\Repository\MessageRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Message
{
    use IdentityTrait;
    use CreatedModifiedTrait;

    /**
     * @var null|string
     *
     * @ORM\Column(type="text")
     */
    private $message;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="was_seen", options={"default" : 0})
     */
    private $wasSeen = 0;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="messages")
     */
    private $sender;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="messages")
     */
    private $receiver;

    /**
     * @return null|string
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param null|string $message
     *
     * @return Message
     */
    public function setMessage(?string $message): Message
    {
        $this->message = $message;

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
     * @param User $user
     *
     * @return Message
     */
    public function setSender(User $user): Message
    {
        $this->sender = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getReceiver(): User
    {
        return $this->receiver;
    }

    /**
     * @param User $user
     *
     * @return Message
     */
    public function setReceiver(User $user): Message
    {
        $this->receiver = $user;

        return $this;
    }

    /**
     * Set wasSeen.
     *
     * @param bool $wasSeen
     *
     * @return Message
     */
    public function setWasSeen($wasSeen): Message
    {
        $this->wasSeen = $wasSeen;

        return $this;
    }

    /**
     * Get wasSeen.
     *
     * @return bool
     */
    public function getWasSeen(): bool
    {
        return $this->wasSeen;
    }
}
