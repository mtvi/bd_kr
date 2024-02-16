<?php

namespace App\Entity;

use App\Repository\MemoryTypesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MemoryTypesRepository::class)]
class MemoryTypes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Type = null;

    #[ORM\Column(nullable: true)]
    private ?int $Speed = null;

    #[ORM\Column(nullable: true)]
    private ?int $BusWidth = null;

    #[ORM\Column(nullable: true)]
    private ?int $BandWidth = null;

    #[ORM\OneToMany(mappedBy: 'Memory', targetEntity: GPU::class)]
    private Collection $GPUs;

    public function __construct()
    {
        $this->GPUs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->Type;
    }

    public function setType(string $Type): static
    {
        $this->Type = $Type;

        return $this;
    }

    public function getSpeed(): ?int
    {
        return $this->Speed;
    }

    public function setSpeed(?int $Speed): static
    {
        $this->Speed = $Speed;

        return $this;
    }

    public function getBusWidth(): ?int
    {
        return $this->BusWidth;
    }

    public function setBusWidth(?int $BusWidth): static
    {
        $this->BusWidth = $BusWidth;

        return $this;
    }

    public function getBandWidth(): ?int
    {
        return $this->BandWidth;
    }

    public function setBandWidth(?int $BandWidth): static
    {
        $this->BandWidth = $BandWidth;

        return $this;
    }

    /**
     * @return Collection<int, GPU>
     */
    public function getGPUs(): Collection
    {
        return $this->GPUs;
    }

    public function addGPUs(GPU $gPUs): static
    {
        if (!$this->GPUs->contains($gPUs)) {
            $this->GPUs->add($gPUs);
            $gPUs->setMemory($this);
        }

        return $this;
    }

    public function removeGPUs(GPU $gPUs): static
    {
        if ($this->GPUs->removeElement($gPUs)) {
            // set the owning side to null (unless already changed)
            if ($gPUs->getMemory() === $this) {
                $gPUs->setMemory(null);
            }
        }

        return $this;
    }
}
