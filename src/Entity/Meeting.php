<?php

namespace App\Entity;

use App\Repository\MeetingRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MeetingRepository::class)]
class Meeting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'meetings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Library $location = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'meetings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'meeting', targetEntity: Attendee::class)]
    private Collection $attendees;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Book $book = null;

    public function __construct()
    {
        $this->attendees = new ArrayCollection();
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

    public function getLocation(): ?Library
    {
        return $this->location;
    }

    public function setLocation(?Library $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Attendee>
     */
    public function getAttendees(): Collection
    {
        return $this->attendees;
    }

    public function addAttendee(Attendee $attendee): self
    {
        if (!$this->attendees->contains($attendee)) {
            $this->attendees->add($attendee);
            $attendee->setMeeting($this);
        }

        return $this;
    }

    public function removeAttendee(Attendee $attendee): self
    {
        if ($this->attendees->removeElement($attendee)) {
            // set the owning side to null (unless already changed)
            if ($attendee->getMeeting() === $this) {
                $attendee->setMeeting(null);
            }
        }

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
}
