<?php

namespace App\Entity;

use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriesRepository::class)]
class Categories
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $CategoryName = null;

    #[ORM\OneToMany(mappedBy: 'Category', targetEntity: GPU::class)]
    private Collection $GPUs;

    public function __construct()
    {
        $this->GPUs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoryName(): ?string
    {
        return $this->CategoryName;
    }

    public function setCategoryName(string $CategoryName): static
    {
        $this->CategoryName = $CategoryName;

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
            $gPUs->setCategory($this);
        }

        return $this;
    }

    public function removeGPUs(GPU $gPUs): static
    {
        if ($this->GPUs->removeElement($gPUs)) {
            // set the owning side to null (unless already changed)
            if ($gPUs->getCategory() === $this) {
                $gPUs->setCategory(null);
            }
        }

        return $this;
    }
}
