<?php
namespace Framework\Traits;

use Doctrine\ORM\Mapping as ORM;

trait IdentityTrait
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }
}
