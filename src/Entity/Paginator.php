<?php

namespace App\Entity;

use App\Repository\PaginatorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PaginatorRepository::class)
 */
class Paginator
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $page;

    /**
     * @ORM\Column(type="integer")
     */

    private $nbPages;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomRoute;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $paramsRoute = [];

    /**
     * @ORM\OneToMany(targetEntity=Haircut::class, mappedBy="paginator", orphanRemoval=true)
     */
    private $haircuts;

    public function __construct()
    {
        $this->haircuts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function setPage(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getNbPage(): ?int
    {
        return $this->nbPage;
    }

    public function setNbPage(int $nbPage): self
    {
        $this->nbPage = $nbPage;
    }

    public function getNomRoute(): ?string
    {
        return $this->nomRoute;
    }

    public function setNomRoute(string $nomRoute): self
    {
        $this->nomRoute = $nomRoute;

        return $this;
    }

    public function getParamsRoute(): ?array
    {
        return $this->paramsRoute;
    }

    public function setParamsRoute(?array $paramsRoute): self
    {
        $this->paramsRoute = $paramsRoute;

        return $this;
    }

    /**
     * @return Collection|Haircut[]
     */
    public function gethaircuts(): Collection
    {
        return $this->haircuts;
    }

    public function addHaircut(Haircut $haircut): self
    {
        if (!$this->haircut->contains($haircut)) {
            $this->haircut[] = $haircut;
            $haircut->setPaginator($this);
        }

        return $this;
    }

    public function removeHaircut(Haircut $haircut): self
    {
        if ($this->haircuts->removeElement($haircut)) {
            // set the owning side to null (unless already changed)
            if ($haircut->getPaginator() === $this) {
                $haircut->setPaginator(null);
            }
        }

        return $this;
    }
}
