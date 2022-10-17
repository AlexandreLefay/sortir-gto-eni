<?php

namespace App\EtatUpdate;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Entity\Sortie;
use Doctrine\Persistence\ManagerRegistry;

class EventUpdate
{

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }


    public function checkClotureOuEnCours($etatActuel) {
        if($etatActuel == 2 || $etatActuel == 3) {
            return true;
        } else {
            return false;
        }
    }

    public function checkClotureOuOuvert($dateActuelleString, $dateCloture, $etatActuel, $nbrInscritMax, $nbrInscrit) {
        if($dateActuelleString < $dateCloture and $etatActuel == 2) {
            return true;
        } elseif($nbrInscrit == $nbrInscritMax) {
            return true;
        } else {
            return false;
        }
    }

    public function checkActEnCours($dateActuelleString, $dateDebut, $dateFin) {
        if($dateActuelleString > $dateDebut && $dateActuelleString < $dateFin) {
            return true;
        } else {
            return false;
        }
    }

    public function calculTempsEcoule($heureDuree, $dateDebut, $dateActuelleString) {

        $DateFin = date('Y-m-d H:i:s',strtotime($heureDuree.' hour',strtotime($dateDebut)));
        $dateFinStrToTime = strtotime($DateFin);
        $dateActuelleStringStr = strtotime($dateActuelleString);
        $jourTotalDepuisFin = ($dateActuelleStringStr-$dateFinStrToTime)/86400;

        return $jourTotalDepuisFin;
    }

    public function updateEtatFlush($etat, $sortie) {

        $entityManager = $this->doctrine->getManager();

        $sortie->setEtat($etat);
        $entityManager->persist($sortie);
        $entityManager->flush();
    }

}
