<?php
namespace Framework\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Framework\Traits\CreatedModifiedTrait;
use Framework\Traits\IdentityTrait;
use Framework\Models\User;

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
     * @ORM\Column(type="integer")
     */
    private $sender;

    /**
     * @ORM\Column(type="string")
     */
    private $chatGroup;

    /**
     * @ORM\Column(type="text")
     */
    private $message;

    /**
     * @return mixed
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param mixed $sender
     */
    public function setSender($sender): void
    {
        $this->sender = $sender;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getChatGroup()
    {
        return $this->chatGroup;
    }

    /**
     * @param mixed $chatGroup
     */
    public function setChatGroup($chatGroup): void
    {
        $this->chatGroup = $chatGroup;
    }
}
