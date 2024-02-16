<?php

namespace App\Entity;

use App\Repository\PCIRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PCIRepository::class)]
class PCI
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $Version = null;

    #[ORM\Column]
    private ?int $BandWidth = null;

    #[ORM\Column]
    private ?int $LanesNumber = null;

    #[ORM\OneToMany(mappedBy: 'PCIVersion', targetEntity: GPU::class)]
    private Collection $GPUs;

    public function __construct()
    {
        $this->GPUs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVersion(): ?string
    {
        return $this->Version;
    }

    public function setVersion(string $Version): static
    {
        $this->Version = $Version;

        return $this;
    }

    public function getBandWidth(): ?int
    {
        return $this->BandWidth;
    }

    public function setBandWidth(int $BandWidth): static
    {
        $this->BandWidth = $BandWidth;

        return $this;
    }

    public function getLanesNumber(): ?int
    {
        return $this->LanesNumber;
    }

    public function setLanesNumber(int $LanesNumber): static
    {
        $this->LanesNumber = $LanesNumber;

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
            $gPUs->setPCIVersion($this);
        }

        return $this;
    }

    public function removeGPUs(GPU $gPUs): static
    {
        if ($this->GPUs->removeElement($gPUs)) {
            // set the owning side to null (unless already changed)
            if ($gPUs->getPCIVersion() === $this) {
                $gPUs->setPCIVersion(null);
            }
        }

        return $this;
    }
}
