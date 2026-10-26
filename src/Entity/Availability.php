<?php

namespace App\Entity;

use App\Repository\AvailabilityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvailabilityRepository::class)]
class Availability
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'availabilities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Library $library = null;

    #[ORM\ManyToOne(inversedBy: 'availabilities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Book $book = null;

    #[ORM\Column]
    private ?int $total_count = null;

    #[ORM\Column]
    private ?int $current_count = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getLibrary(): ?Library
    {
        return $this->library;
    }

    public function setLibrary(?Library $library): self
    {
        $this->library = $library;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;

        return $this;
    }

    public function getTotalCount(): ?int
    {
        return $this->total_count;
    }

    public function setTotalCount(int $total_count): self
    {
        $this->total_count = $total_count;

        return $this;
    }

    public function getCurrentCount(): ?int
    {
        return $this->current_count;
    }

    public function setCurrentCount(int $current_count): self
    {
        $this->current_count = $current_count;

        return $this;
    }
}
