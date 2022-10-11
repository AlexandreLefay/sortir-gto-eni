<?php

namespace App\Entity;

use App\Repository\SiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteRepository::class)]
class Site
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'site', targetEntity: User::class)]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'site', targetEntity: Sortie::class)]
    private Collection $siteOrganisateur;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->siteOrganisateur = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUsers(User $users): self
    {
        if (!$this->users->contains($users)) {
            $this->users->add($users);
            $users->setSite($this);
        }

        return $this;
    }

    public function removeUsers(User $users): self
    {
        if ($this->users->removeElement($users)) {
            // set the owning side to null (unless already changed)
            if ($users->getSite() === $this) {
                $users->setSite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSiteOrganisateur(): Collection
    {
        return $this->siteOrganisateur;
    }

    public function addSiteOrganisateur(Sortie $siteOrganisateur): self
    {
        if (!$this->siteOrganisateur->contains($siteOrganisateur)) {
            $this->siteOrganisateur->add($siteOrganisateur);
            $siteOrganisateur->setSite($this);
        }

        return $this;
    }

    public function removeSiteOrganisateur(Sortie $siteOrganisateur): self
    {
        if ($this->siteOrganisateur->removeElement($siteOrganisateur)) {
            // set the owning side to null (unless already changed)
            if ($siteOrganisateur->getSite() === $this) {
                $siteOrganisateur->setSite(null);
            }
        }

        return $this;
    }
}
