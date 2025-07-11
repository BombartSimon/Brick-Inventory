<?php

namespace App\Entity;

use App\Repository\SetPartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SetPartRepository::class)]
class SetPart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $partNum = null;

    #[ORM\Column(length: 255)]
    private ?string $partName = null;

    #[ORM\Column]
    private ?int $colorId = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\ManyToOne(inversedBy: 'SetParts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Set $set = null;

    /**
     * @var Collection<int, MissingPart>
     */
    #[ORM\OneToMany(targetEntity: MissingPart::class, mappedBy: 'part', cascade: ['remove'])]
    private Collection $missingParts;

    public function __construct()
    {
        $this->missingParts = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPartNum(): ?string
    {
        return $this->partNum;
    }

    public function setPartNum(string $partNum): static
    {
        $this->partNum = $partNum;

        return $this;
    }

    public function getPartName(): ?string
    {
        return $this->partName;
    }

    public function setPartName(string $partName): static
    {
        $this->partName = $partName;

        return $this;
    }

    public function getColorId(): ?int
    {
        return $this->colorId;
    }

    public function setColorId(int $colorId): static
    {
        $this->colorId = $colorId;

        return $this;
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

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getSet(): ?Set
    {
        return $this->set;
    }

    public function setSet(?Set $set): static
    {
        $this->set = $set;

        return $this;
    }

    /**
     * @return Collection<int, MissingPart>
     */
    public function getMissingParts(): Collection
    {
        return $this->missingParts;
    }

    public function addMissingPart(MissingPart $missingPart): static
    {
        if (!$this->missingParts->contains($missingPart)) {
            $this->missingParts->add($missingPart);
            $missingPart->setPart($this);
        }

        return $this;
    }

    public function removeMissingPart(MissingPart $missingPart): static
    {
        if ($this->missingParts->removeElement($missingPart)) {
            // set the owning side to null (unless already changed)
            if ($missingPart->getPart() === $this) {
                $missingPart->setPart(null);
            }
        }

        return $this;
    }
}
