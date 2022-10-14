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


}