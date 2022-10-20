<?php

namespace App\Entity;

class SearchData
{
    public $site;
    public $searchbar = '';
    public $organisateur;
    public $inscrit;
    public $nonInscrit;
    public $passees;
    public ?\DateTimeInterface $dateSortieDebut;
    public ?\DateTimeInterface $dateSortieFin;

    /**
     * @return mixed
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param mixed $site
     */
    public function setSites($site): void
    {
        $this->site = $site;
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
    public function getDateSortieDebut(): ?\DateTimeInterface
    {
        return $this->dateSortieDebut;
    }

    /**
     * @param \DateTimeInterface|null $dateSortieDebut
     */
    public function setDateSortieDebut(?\DateTimeInterface $dateSortieDebut): void
    {
        $this->dateSortieDebut = $dateSortieDebut;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateSortieFin(): ?\DateTimeInterface
    {
        return $this->dateSortieFin;
    }

    /**
     * @param \DateTimeInterface|null $dateSortieFin
     */
    public function setDateSortieFin(?\DateTimeInterface $dateSortieFin): void
    {
        $this->dateSortieFin = $dateSortieFin;
    }


}