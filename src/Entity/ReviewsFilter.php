<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class ReviewsFilter
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $orderRating;

    /**
     * @ORM\Column(type="string")
     */
    private $minRating;

    /**
     * @ORM\Column(type="string")
     */
    private $orderDate;

    /**
     * @ORM\Column(type="string")
     */
    private $textPriority;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderRating(): ?string
    {
        return $this->orderRating;
    }

    public function setOrderRating(string $orderRating): self
    {
        $this->orderRating = $orderRating;

        return $this;
    }

    public function getMinRating(): ?string
    {
        return $this->minRating;
    }

    public function setMinRating(string $minRating): self
    {
        $this->minRating = $minRating;

        return $this;
    }

    public function getOrderDate(): ?string
    {
        return $this->orderDate;
    }

    public function setOrderDate(string $orderDate): self
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    public function getTextPriority(): ?string
    {
        return $this->textPriority;
    }

    public function setTextPriority(string $textPriority): self
    {
        $this->textPriority = $textPriority;

        return $this;
    }
}
