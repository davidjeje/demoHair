<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity(
 *  fields="email",
 *  errorPath="email",
 *  message="This email is already in use on that data base.I invite you to inform another email. thanks for your understanding.")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message = "Both passwords must be the same in addition to this value should not be blank.")
     */
    private $password;

    /** 
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $roles = []; 

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @ORM\OneToMany(targetEntity=Event::class, mappedBy="member")
     */
    private $events;

    /**
     * @ORM\OneToMany(targetEntity=EventNotValidated::class, mappedBy="customer", orphanRemoval=true)
     */
    private $eventNotValidated;

    /**
     * @ORM\OneToMany(targetEntity=Date::class, mappedBy="member")
     */
    private $date;

    public function __construct()
    {
        $this->isActive = false;
        $this->events = new ArrayCollection();
        $this->eventNotValidated = new ArrayCollection();
        $this->date = new ArrayCollection();
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    
    public function getUsername()
    {
        return $this->name;
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function eraseCredentials()
    {
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(
            array(
            $this->id,
            $this->name,
            $this->password,
            $this->isActive,
            $this->email,
            $this->number,
                
            )
        );
    } 

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list(
                $this->id,
                $this->name,
                $this->password,
                $this->isActive,
                $this->email,
                $this->number,
            ) = unserialize($serialized);
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

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
            $event->setMember($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getMember() === $this) {
                $event->setMember(null);
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
            $eventNotValidated->setCustomer($this);
        }

        return $this;
    }

    public function removeEventNotValidated(EventNotValidated $eventNotValidated): self
    {
        if ($this->eventNotValidated->removeElement($eventNotValidated)) {
            // set the owning side to null (unless already changed)
            if ($eventNotValidated->getCustomer() === $this) {
                $eventNotValidated->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Date[]
     */
    public function getDate(): Collection
    {
        return $this->date;
    }

    public function addDate(Date $date): self
    {
        if (!$this->date->contains($date)) {
            $this->date[] = $date;
            $date->setMember($this);
        }

        return $this;
    }

    public function removeDate(Date $date): self
    {
        if ($this->date->removeElement($date)) {
            // set the owning side to null (unless already changed)
            if ($date->getMember() === $this) {
                $date->setMember(null);
            }
        }

        return $this;
    }
}
