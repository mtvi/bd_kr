<?php

namespace App\Entity;

use App\Repository\ProductsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductsRepository::class)]
class Products
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?GPU $GPU = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Vendors $Vendor = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $Price = null;

    #[ORM\Column(length: 255)]
    private ?string $CoolingType = null;

    #[ORM\Column()]
    private ?int $Views = 0;

    #[ORM\Column()]
    private ?int $Quantity = 0;

    #[ORM\Column()]
    private ?float $Rating = 0;

    #[ORM\OneToMany(mappedBy: 'Product', targetEntity: OrderDetails::class)]
    private Collection $orderDetails;

    #[ORM\OneToMany(mappedBy: 'Product', targetEntity: Reviews::class)]
    private Collection $reviews;

    public function __construct()
    {
        $this->orderDetails = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGPU(): ?GPU
    {
        return $this->GPU;
    }

    public function setGPU(?GPU $GPU): static
    {
        $this->GPU = $GPU;

        return $this;
    }

    public function getVendor(): ?Vendors
    {
        return $this->Vendor;
    }

    public function setVendor(?Vendors $Vendor): static
    {
        $this->Vendor = $Vendor;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->Price;
    }

    public function setPrice(string $Price): static
    {
        $this->Price = $Price;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $path): static
    {
        $this->image = $path;

        return $this;
    }

    public function getCoolingType(): ?string
    {
        return $this->CoolingType;
    }

    public function setCoolingType(string $CoolingType): static
    {
        $this->CoolingType = $CoolingType;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->Quantity;
    }

    public function setQuantity(int $Quantity): static
    {
        $this->Quantity = $Quantity;

        return $this;
    }

    public function getViews(): ?int
    {
        return $this->Views;
    }

    public function setViews(int $Views): static
    {
        $this->Views = $Views;

        return $this;
    }
    public function setView(): static
    {
        $this->Views += 1;

        return $this;
    }
    public function Ordered(int $order): static
    {
        $this->Quantity -= $order;

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->Rating;
    }

    public function setRating(float $Rating): static
    {
        $this->Rating = $Rating;

        return $this;
    }

    /**
     * @return Collection<int, OrderDetails>
     */
    public function getOrderDetails(): Collection
    {
        return $this->orderDetails;
    }

    public function addOrderDetail(OrderDetails $orderDetail): static
    {
        if (!$this->orderDetails->contains($orderDetail)) {
            $this->orderDetails->add($orderDetail);
            $orderDetail->setProduct($this);
        }

        return $this;
    }

    public function removeOrderDetail(OrderDetails $orderDetail): static
    {
        if ($this->orderDetails->removeElement($orderDetail)) {
            // set the owning side to null (unless already changed)
            if ($orderDetail->getProduct() === $this) {
                $orderDetail->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reviews>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Reviews $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setProduct($this);
        }

        return $this;
    }

    public function removeReview(Reviews $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getProduct() === $this) {
                $review->setProduct(null);
            }
        }

        return $this;
    }
}
