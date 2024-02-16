<?php

namespace App\Entity;

use App\Repository\ReviewsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReviewsRepository::class)]
class Reviews
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    private ?Products $Product = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $ReviewDate = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $ReviewText = null;

    #[ORM\Column]
    private ?int $Rating = null;

    #[ORM\Column(length: 50)]
    private ?string $Reviewer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Products
    {
        return $this->Product;
    }

    public function setProduct(?Products $Product): static
    {
        $this->Product = $Product;

        return $this;
    }

    public function getReviewDate(): ?\DateTimeInterface
    {
        return $this->ReviewDate;
    }

    public function setReviewDate(\DateTimeInterface $ReviewDate): static
    {
        $this->ReviewDate = $ReviewDate;

        return $this;
    }

    public function getReviewText(): ?string
    {
        return $this->ReviewText;
    }

    public function setReviewText(string $ReviewText): static
    {
        $this->ReviewText = $ReviewText;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->Rating;
    }

    public function setRating(int $Rating): static
    {
        $this->Rating = $Rating;

        return $this;
    }

    public function getReviewer(): ?string
    {
        return $this->Reviewer;
    }

    public function setReviewer(string $Reviewer): static
    {
        $this->Reviewer = $Reviewer;

        return $this;
    }
}
