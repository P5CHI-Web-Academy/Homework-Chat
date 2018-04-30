<?php
namespace Framework\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Framework\Traits\CreatedModifiedTrait;
use Framework\Traits\IdentityTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="users",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="user_unique_email",columns={"email"})})
 * @ORM\Entity(repositoryClass="Framework\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class User
{
    use IdentityTrait;
    use CreatedModifiedTrait;

    /**
     * @var null|string
     *
     * @ORM\Column(type="string")
     */
    private $email;

    /**
     * @var null|string
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var null|string
     *
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var Message[]
     *
     * @ORM\OneToMany(targetEntity="Message", mappedBy="user")
     */
    private $messages;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }


    /**
     * @return null|string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param null|string $email
     *
     * @return User
     */
    public function setEmail(?string $email): User
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param null|string $password
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    /**
     * Populate user data from request
     */
    public function populate()
    {
        isset($_POST['email']) ? $this->setEmail($_POST['email']) : false;
        isset($_POST['name']) ? $this->setName($_POST['name']) : false;
        isset($_POST['password']) ? $this->setPassword($_POST['password']) : false;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'User';
    }

    /**
     * @ORM\PrePersist()
     */
    public function securePassword()
    {
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
    }
}
