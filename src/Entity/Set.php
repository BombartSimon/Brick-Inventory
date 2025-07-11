<?php

namespace App\Entity;

use App\Repository\SetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SetRepository::class)]
class Set
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $setNum = null;

    #[ORM\Column(length: 255)]
    private ?string $year = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $importedAt = null;

    /**
     * @var Collection<int, SetPart>
     */
    #[ORM\OneToMany(targetEntity: SetPart::class, mappedBy: 'set', cascade: ['remove'])]
    private Collection $SetParts;

    public function __construct()
    {
        $this->SetParts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSetNum(): ?string
    {
        return $this->setNum;
    }

    public function setSetNum(string $setNum): static
    {
        $this->setNum = $setNum;

        return $this;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(string $year): static
    {
        $this->year = $year;

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

    public function getImportedAt(): ?\DateTimeImmutable
    {
        return $this->importedAt;
    }

    public function setImportedAt(\DateTimeImmutable $importedAt): static
    {
        $this->importedAt = $importedAt;

        return $this;
    }

    /**
     * @return Collection<int, SetPart>
     */
    public function getSetParts(): Collection
    {
        return $this->SetParts;
    }

    public function addSetPart(SetPart $SetPart): static
    {
        if (!$this->SetParts->contains($SetPart)) {
            $this->SetParts->add($SetPart);
            $SetPart->setSet($this);
        }

        return $this;
    }

    public function removeSetPart(SetPart $SetPart): static
    {
        if ($this->SetParts->removeElement($SetPart)) {
            // set the owning side to null (unless already changed)
            if ($SetPart->getSet() === $this) {
                $SetPart->setSet(null);
            }
        }

        return $this;
    }
}
