<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $title = null;

    #[ORM\Column(length: 60)]
    private ?string $author = null;

    #[ORM\Column]
    private ?float $average_rating = null;

    #[ORM\Column(nullable: true)]
    private ?bool $trending = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $cover_image = null;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: Review::class)]
    private Collection $reviews;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: FavoriteBook::class)]
    private Collection $favoriteBooks;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: Availability::class)]
    private Collection $availabilities;


    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->favoriteBooks = new ArrayCollection();
        $this->availabilities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getAverageRating(): ?float
    {
        return $this->average_rating;
    }

    public function setAverageRating(float $average_rating): self
    {
        $this->average_rating = $average_rating;

        return $this;
    }

    public function isTrending(): ?bool
    {
        return $this->trending;
    }

    public function setTrending(bool $trending): self
    {
        $this->trending = $trending;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->cover_image;
    }

    public function setCoverImage(string $cover_image): self
    {
        $this->cover_image = $cover_image;

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setBook($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getBook() === $this) {
                $review->setBook(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FavoriteBook>
     */
    public function getFavoriteBooks(): Collection
    {
        return $this->favoriteBooks;
    }

    public function addFavoriteBook(FavoriteBook $favoriteBook): self
    {
        if (!$this->favoriteBooks->contains($favoriteBook)) {
            $this->favoriteBooks->add($favoriteBook);
            $favoriteBook->setBook($this);
        }

        return $this;
    }

    public function removeFavoriteBook(FavoriteBook $favoriteBook): self
    {
        if ($this->favoriteBooks->removeElement($favoriteBook)) {
            // set the owning side to null (unless already changed)
            if ($favoriteBook->getBook() === $this) {
                $favoriteBook->setBook(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Availability>
     */
    public function getAvailabilities(): Collection
    {
        return $this->availabilities;
    }

    public function addAvailability(Availability $availability): self
    {
        if (!$this->availabilities->contains($availability)) {
            $this->availabilities->add($availability);
            $availability->setBook($this);
        }

        return $this;
    }

    public function removeAvailability(Availability $availability): self
    {
        if ($this->availabilities->removeElement($availability)) {
            // set the owning side to null (unless already changed)
            if ($availability->getBook() === $this) {
                $availability->setBook(null);
            }
        }

        return $this;
    }
}
