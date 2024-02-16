<?php

namespace App\Entity;

use App\Repository\ManufacturersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ManufacturersRepository::class)]
class Manufacturers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $ManufacturerName = null;

    #[ORM\Column(length: 50)]
    private ?string $Website = null;

    #[ORM\Column(length: 50)]
    private ?string $Country = null;

    #[ORM\OneToMany(mappedBy: 'Manufacturer', targetEntity: GPU::class)]
    private Collection $GPUs;

    public function __construct()
    {
        $this->GPUs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getManufacturerName(): ?string
    {
        return $this->ManufacturerName;
    }

    public function setManufacturerName(string $ManufacturerName): static
    {
        $this->ManufacturerName = $ManufacturerName;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->Website;
    }

    public function setWebsite(string $Website): static
    {
        $this->Website = $Website;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->Country;
    }

    public function setCountry(string $Country): static
    {
        $this->Country = $Country;

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
            $gPUs->setManufacturer($this);
        }

        return $this;
    }

    public function removeGPUs(GPU $gPUs): static
    {
        if ($this->GPUs->removeElement($gPUs)) {
            // set the owning side to null (unless already changed)
            if ($gPUs->getManufacturer() === $this) {
                $gPUs->setManufacturer(null);
            }
        }

        return $this;
    }
}
