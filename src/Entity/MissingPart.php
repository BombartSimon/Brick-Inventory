<?php

namespace App\Entity;

use App\Repository\MissingPartRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MissingPartRepository::class)]
class MissingPart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'missingParts')]
    private ?SetPart $part = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPart(): ?SetPart
    {
        return $this->part;
    }

    public function setPart(?SetPart $part): static
    {
        $this->part = $part;

        return $this;
    }
}
