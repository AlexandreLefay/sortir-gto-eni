<?php

namespace App\Command;

use App\EtatUpdate;
use App\EtatUpdate\EventUpdate;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Flex\Event\UpdateEvent;

#[AsCommand(
    name: 'app:etat-update',
    description: 'Update des états',
)]
class EtatUpdateCommand extends Command
{
    private $entityManager;


    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    public function __construct(SortieRepository $sortieRepository, EtatRepository $etatRepository, EventUpdate $updateEvent)
    {
        $this->sortieRepository = $sortieRepository;
        $this->etatRepository = $etatRepository;
        $this->updateEvent = $updateEvent;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Execution de la commande'
        ]);

        $sortieRepository = $this->sortieRepository;
        $etatRepository = $this->etatRepository;
        $updateEvent = $this->updateEvent;

        //      Changement des états en fonction des dates etc (Je dois refactoriser un """peu""" le code parce que c'est pas très beau, MAIS ça marche)
        $sorties = $sortieRepository->findAll();
        date_default_timezone_set('Europe/Paris');
        $dateActuelle = new \DateTime("now");
        $dateActuelleString = $dateActuelle->format('Y-m-d H:i:s');

        foreach ($sorties as $sortie) {

            $Users = $sortie->getUsers();
            $nbrUsersInscrit = count($Users);
            $nbrInscritMax = $sortie->getNbInscriptionsMax();
            for ($i = 0; $i < 5; $i++) {
//                Récupération des dates et états pour chaque sortie
                $etatActuel = $sortie->getEtat()->getId();
                $dateDebut = $sortie->getDateDebut()->format('Y-m-d H:i:s');
                $dateCloture = $sortie->getDateCloture()->format('Y-m-d H:i:s');
                $heureDuree = $sortie->getDuree();
                $DateFin = date('Y-m-d H:i:s', strtotime($heureDuree . ' hour', strtotime($dateDebut)));
//              Fonction qui calcul le temps écoulé depuis la fin en jours
                $calculTempsEcoule = $updateEvent->calculTempsEcoule($heureDuree, $dateDebut, $dateActuelleString);

//              Fonctions qui checks les états, si l'activité est en cours et les retournes pour les conditions
                $checkEtatClotureEnCour = $updateEvent->checkClotureOuEnCours($etatActuel);
                $checkActEnCours = $updateEvent->checkActEnCours($dateActuelleString, $dateDebut, $DateFin);
//                Fonction qui check les nbrInscrit et par rapport à la date de cloture et retourne un boolean
                $checkClotureOuOuvert = $updateEvent->checkClotureOuOuvert($dateActuelleString, $dateCloture, $etatActuel, $nbrInscritMax, $nbrUsersInscrit);



//                Si la date de cloture est arrivé ou que le nombre de participants max est atteint etat = cloturee
                if ($checkClotureOuOuvert == true) {
                    $etat = $etatRepository->findOneBy([
                        "id" => 3
                    ]);
                    $updateEvent->updateEtatFlush($etat, $sortie);
                }

//              Si la sortie est à l'etat publie(cloturée) et que le nombre user_max < aux user inscrit, -- Fonctionne
                if (!$checkClotureOuOuvert and $etatActuel = "clôturée") {

                    $etat = $etatRepository->findOneBy([
                        "id" => 2
                    ]);
                    $updateEvent->updateEtatFlush($etat, $sortie);
                }

//              Si la sortie est à l'etat publie(ouverte) ou publie(cloturee),  à commencé et n'est pas terminé etat = act en cours' - Fonctionne
                if ($checkActEnCours == true && $checkEtatClotureEnCour == true) {

                    $etat = $etatRepository->findOneBy([
                        "id" => 4
                    ]);
                    $updateEvent->updateEtatFlush($etat, $sortie);
                }

//              Si la date de fin de sortie est inferieur à la date actuelle et que la sortie etait en cours alors etat = passe - Fonctionne
                if ($dateActuelleString > $DateFin && $etatActuel == 4) {

                    $etat = $etatRepository->findOneBy([
                        "id" => 5
                    ]);
                    $updateEvent->updateEtatFlush($etat, $sortie);
                }

//              Si la date de fin de sortie est superieur à 1 mois et que l'etat actuel est passé alors etat = archive - Fonctionne
                if ($calculTempsEcoule > 30 and $etatActuel == 5) {
//                dd($jourTotalDepuisFin);
                    $etat = $etatRepository->findOneBy([
                        "id" => 7
                    ]);
                    $updateEvent->updateEtatFlush($etat, $sortie);
                }
            }
        }
        return 1;
    }
}
