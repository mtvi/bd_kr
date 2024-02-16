<?php

namespace App\Entity;

use App\Repository\GPURepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GPURepository::class)]
class GPU
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'GPUs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Manufacturers $Manufacturer = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $ReleaseDate = null;

    #[ORM\ManyToOne(inversedBy: 'GPUs')]
    private ?MemoryTypes $Memory = null;

    #[ORM\Column]
    private ?int $CoreClock = null;

    #[ORM\Column]
    private ?int $BoostClock = null;

    #[ORM\Column]
    private ?int $VRAM = null;

    #[ORM\Column]
    private ?int $TDP = null;

    #[ORM\ManyToOne(inversedBy: 'GPUs')]
    private ?PCI $PCIVersion = null;

    #[ORM\ManyToOne(inversedBy: 'GPUs')]
    private ?Categories $Category = null;

    #[ORM\OneToMany(mappedBy: 'GPU', targetEntity: Products::class)]
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getManufacturer(): ?Manufacturers
    {
        return $this->Manufacturer;
    }

    public function setManufacturer(?Manufacturers $Manufacturer): static
    {
        $this->Manufacturer = $Manufacturer;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): static
    {
        $this->Name = $Name;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->ReleaseDate;
    }

    public function setReleaseDate(\DateTimeInterface $ReleaseDate): static
    {
        $this->ReleaseDate = $ReleaseDate;

        return $this;
    }

    public function getMemory(): ?MemoryTypes
    {
        return $this->Memory;
    }

    public function setMemory(?MemoryTypes $Memory): static
    {
        $this->Memory = $Memory;

        return $this;
    }

    public function getCoreClock(): ?int
    {
        return $this->CoreClock;
    }

    public function setCoreClock(int $CoreClock): static
    {
        $this->CoreClock = $CoreClock;

        return $this;
    }

    public function getBoostClock(): ?int
    {
        return $this->BoostClock;
    }

    public function setBoostClock(int $BoostClock): static
    {
        $this->BoostClock = $BoostClock;

        return $this;
    }

    public function getVRAM(): ?int
    {
        return $this->VRAM;
    }

    public function setVRAM(int $VRAM): static
    {
        $this->VRAM = $VRAM;

        return $this;
    }

    public function getTDP(): ?int
    {
        return $this->TDP;
    }

    public function setTDP(int $TDP): static
    {
        $this->TDP = $TDP;

        return $this;
    }

    public function getPCIVersion(): ?PCI
    {
        return $this->PCIVersion;
    }

    public function setPCIVersion(?PCI $PCIVersion): static
    {
        $this->PCIVersion = $PCIVersion;

        return $this;
    }

    public function getCategory(): ?Categories
    {
        return $this->Category;
    }

    public function setCategory(?Categories $Category): static
    {
        $this->Category = $Category;

        return $this;
    }

    /**
     * @return Collection<int, Products>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Products $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setGPU($this);
        }

        return $this;
    }

    public function removeProduct(Products $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getGPU() === $this) {
                $product->setGPU(null);
            }
        }

        return $this;
    }
}
