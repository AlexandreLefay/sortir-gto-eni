<?php

namespace App\Entity;

class SearchData
{
    public $sites;
    public $searchbar = '';
    public $organisateur;
    public $inscrit ;
    public $nonInscrit;
    public $passees;
    public ?\DateTimeInterface $dateSortie = null;
    public ?\DateTimeInterface $dateCloture = null;

    /**
     * @return mixed
     */
    public function getSites()
    {
        return $this->sites;
    }

    /**
     * @param mixed $sites
     */
    public function setSites($sites): void
    {
        $this->sites = $sites;
    }


    /**
     * @return string
     */
    public function getSearchbar(): string
    {
        return $this->searchbar;
    }

    /**
     * @param string $searchbar
     */
    public function setSearchbar(string $searchbar): void
    {
        $this->searchbar = $searchbar;
    }

    /**
     * @return mixed
     */
    public function getOrganisateur()
    {
        return $this->organisateur;
    }

    /**
     * @param mixed $organisateur
     */
    public function setOrganisateur($organisateur): void
    {
        $this->organisateur = $organisateur;
    }

    /**
     * @return mixed
     */
    public function getInscrit()
    {
        return $this->inscrit;
    }

    /**
     * @param mixed $inscrit
     */
    public function setInscrit($inscrit): void
    {
        $this->inscrit = $inscrit;
    }

    /**
     * @return mixed
     */
    public function getNonInscrit()
    {
        return $this->nonInscrit;
    }

    /**
     * @param mixed $nonInscrit
     */
    public function setNonInscrit($nonInscrit): void
    {
        $this->nonInscrit = $nonInscrit;
    }

    /**
     * @return mixed
     */
    public function getPassees()
    {
        return $this->passees;
    }

    /**
     * @param mixed $passees
     */
    public function setPassees($passees): void
    {
        $this->passees = $passees;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateSortie(): ?\DateTimeInterface
    {
        return $this->dateSortie;
    }

    /**
     * @param \DateTimeInterface|null $dateSortie
     */
    public function setDateSortie(?\DateTimeInterface $dateSortie): void
    {
        $this->dateSortie = $dateSortie;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateCloture(): ?\DateTimeInterface
    {
        return $this->dateCloture;
    }

    /**
     * @param \DateTimeInterface|null $dateCloture
     */
    public function setDateCloture(?\DateTimeInterface $dateCloture): void
    {
        $this->dateCloture = $dateCloture;
    }

}