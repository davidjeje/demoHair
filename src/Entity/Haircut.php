<?php

namespace App\Entity;

use App\Repository\HaircutRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HaircutRepository::class)
 */
class Haircut
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;
 
    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /** 
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity=Paginator::class, inversedBy="haircuts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $paginator;

    /**
     * @ORM\OneToMany(targetEntity=Event::class, mappedBy="haircut")
     */
    private $events;

    /**
     * @ORM\OneToMany(targetEntity=EventNotValidated::class, mappedBy="haircut", orphanRemoval=true)
     */
    private $eventNotValidated;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->eventNotValidated = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getPaginator(): ?Paginator
    {
        return $this->paginator;
    }

    public function setPaginator(?Paginator $paginator): self
    {
        $this->paginator = $paginator;

        return $this;
    }

    /**
     * @return Collection|Event[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setHaircut($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getHaircut() === $this) {
                $event->setHaircut(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|EventNotValidated[]
     */
    public function getEventNotValidated(): Collection
    {
        return $this->eventNotValidated;
    }

    public function addEventNotValidated(EventNotValidated $eventNotValidated): self
    {
        if (!$this->eventNotValidated->contains($eventNotValidated)) {
            $this->eventNotValidated[] = $eventNotValidated;
            $eventNotValidated->setHair($this);
        }

        return $this;
    }

    public function removeEventNotValidated(EventNotValidated $eventNotValidated): self
    {
        if ($this->eventNotValidated->removeElement($eventNotValidated)) {
            // set the owning side to null (unless already changed)
            if ($eventNotValidated->getHair() === $this) {
                $eventNotValidated->setHair(null);
            }
        }

        return $this;
    }
}
