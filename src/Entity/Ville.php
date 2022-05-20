<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VilleRepository::class)
 */
class Ville
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $libelé;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelé(): ?string
    {
        return $this->libelé;
    }

    public function setLibelé(string $libelé): self
    {
        $this->libelé = $libelé;

        return $this;
    }
}
