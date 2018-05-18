<?php
namespace Framework\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

trait CreatedModifiedTrait
{
    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    private $modifiedAt;

    /**
     * @return null|DateTime
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return null|DateTime
     */
    public function getModifiedAt(): ?DateTime
    {
        return $this->modifiedAt;
    }

    /**
     * @param DateTime $modifiedAt
     *
     * @return $this
     */
    public function setModifiedAt(DateTime $modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * @ORM\PrePersist
     *
     * @return void
     */
    public function setCreatedAtValue(): void
    {
        if (!$this->createdAt) {
            $this->createdAt = new DateTime();
        }
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * @return void
     */
    public function setModifiedAtValue(): void
    {
        $this->modifiedAt = new DateTime();
    }
}
