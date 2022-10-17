<?php

namespace App\Controller;

use App\EtatUpdate;
use App\Entity\User;
use App\Entity\Lieu;
use App\Entity\SearchData;
use App\Entity\Sortie;
use App\EtatUpdate\EventUpdate;
use App\Form\LieuType;
use App\Form\SearchFormType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'app_sortie_index', methods: ['GET', 'POST'])]


   public function index(
        SortieRepository $sortieRepository,
        EtatRepository $etatRepository,
        EntityManagerInterface $entityManager,
        Request $request,
        EventUpdate $updateEvent,
        UserRepository $userRepository
    ): Response

    {
//        Pour le cookie afin de garder en mémoire l'adresse mail si checkbox
        $res = new Response();
        $res->headers->clearCookie('mail');
        $res->send();

        if($request->cookies->get('REMEMBERME')){
            $cookie = new Cookie ('mail',$this->getUser()->getUserIdentifier() );
            $res = new Response();
            $res->headers->setCookie($cookie);
            $res->send();
        }
//      Changement des états en fonction des dates etc (Je dois refactoriser un """peu""" le code parce que c'est pas très beau, MAIS ça marche)
        $sorties =  $sortieRepository->findAll();
        date_default_timezone_set('Europe/Paris');
        $dateActuelle = new \DateTime("now");
        $dateActuelleString = $dateActuelle->format('Y-m-d H:i:s');

        foreach ($sorties as $sortie ) {
            for ($i=0; $i<5; $i++) {
//                Récupération des dates et états pour chaque sortie
                $etatActuel = $sortie->getEtat()->getId();
                $dateDebut = $sortie->getDateDebut()->format('Y-m-d H:i:s');
                $dateCloture = $sortie->getDateCloture()->format('Y-m-d H:i:s');
                $heureDuree = $sortie->getDuree();
                $DateFin = date('Y-m-d H:i:s',strtotime($heureDuree.' hour',strtotime($dateDebut)));
//              Fonction qui calcul le temps écoulé depuis la fin en jours
                $calculTempsEcoule = $updateEvent->calculTempsEcoule($heureDuree, $dateDebut, $dateActuelleString );

//              Fonctions qui checks les états, si l'activité est en cours et les retournes pour les conditions
                $checkEtatClotureEnCour = $updateEvent->checkClotureOuEnCours($etatActuel);
                $checkActEnCours = $updateEvent->checkActEnCours($dateActuelleString, $dateDebut, $DateFin);

//              Si la date de cloture est arrivé ou que le nombre de participants max est atteint etat = cloturee
//              FONCTIONNE mais il faut aussi rajouter le check pour le nombre de participants
                if ($dateActuelleString > $dateCloture and $etatActuel == 2)  {

                    $etat = $etatRepository->findOneBy([
                        "id" => 3
                    ]);
                    $updateEvent->updateEtat($etat, $sortie);
                }

//              Si la sortie est à l'etat publie(ouverte) ou publie(cloturee),  à commencé et n'est pas terminé etat = act en cours' - Fonctionne
                if($checkActEnCours == true && $checkEtatClotureEnCour == true) {

                    $etat = $etatRepository->findOneBy([
                        "id" => 4
                    ]);
                    $updateEvent->updateEtat($etat, $sortie);
                }

//              Si la date de fin de sortie est inferieur à la date actuelle et que la sortie etait en cours alors etat = passe - Fonctionne
                if ($dateActuelleString > $DateFin && $etatActuel == 4 )  {

                    $etat = $etatRepository->findOneBy([
                        "id" => 5
                    ]);
                    $updateEvent->updateEtat($etat, $sortie);
                }

//              Si la date de fin de sortie est superieur à 1 mois et que l'etat actuel est passé alors etat = archive - Fonctionne
                if ($calculTempsEcoule > 30 and $etatActuel == 5) {
//                dd($jourTotalDepuisFin);
                    $etat = $etatRepository->findOneBy([
                        "id" => 7
                    ]);
                    $updateEvent->updateEtat($etat, $sortie);
                }
            }
        }

//        Pour les filtres c'est un peu compliqué
        $search = new SearchData();
        $formSearch = $this->createForm(SearchFormType::class, $search,[
            'action' => $this->generateUrl('app_sortie_index'),
            'method' => 'POST',
            ]);
        $formSearch->handleRequest($request);
        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $orgaCheckbox = $search->getOrganisateur();
            $mesSortiesCheckbox = $search->getInscrit();
            $nonInscritCheckbox = $search->getNonInscrit();
            $sortiesFiniesCheckbox = $search->getPassees();
            if($orgaCheckbox){
                return $this->render('sortie/index.html.twig', [
                    'sorties' => $sortieRepository->findBy(
                    ['user' => $this->getUser()->getId()]),
                    'formSearch' =>$formSearch->createView(),
                    'dateNow' => $dateActuelleString
                ]);
            }
            if($mesSortiesCheckbox){
                return $this->render('sortie/index.html.twig', [
                    'sorties' => $this->getUser()->getInscrit(),
                    'formSearch' =>$formSearch->createView(),
                    'dateNow' => $dateActuelleString
                ]);
            }
            if($nonInscritCheckbox){
                $userConnected = $this->getUser()->getId();
                return $this->render('sortie/index.html.twig', [
                    'sorties' => $sortieRepository->findNotSubscribeEvent($userConnected),
                    'formSearch' =>$formSearch->createView(),
                    'dateNow' => $dateActuelleString
                ]);
            }
            if($sortiesFiniesCheckbox){
                 dd($sortieRepository->findFinishedEvent());
                 return $this->render('sortie/index.html.twig', [
                    'sorties' => $sortieRepository->findFinishedEvent(),
                    'formSearch' =>$formSearch->createView(),
                    'dateNow' => $dateActuelleString
                ]);
            }
        }
        return $this->render('sortie/index.html.twig', [
            'sorties' => $sortieRepository->findAll(), 'formSearch' =>$formSearch->createView(), 'dateNow' => $dateActuelleString
        ]);
    }

    #[Route('/new', name: 'app_sortie_new', methods: ['GET', 'POST'])]
    public function add(Request $request, UserRepository $user, EtatRepository $etatRepository, SortieRepository $sortieRepository, EntityManagerInterface $entityManager): Response
    {
        $sortie = new Sortie();
        $formSortie = $this->createForm(SortieType::class, $sortie);
        $formSortie->handleRequest($request);

        if ($formSortie->isSubmitted() && $formSortie->isValid()) {
            //Il y a deux bouton différents, un pour enregistrer et l'autre pour publier
            //en fonction du bouton l'état de la sortie ne sera pas le même
            if ($formSortie->getClickedButton() === $formSortie->get('save')) {
                $etat = $etatRepository->findOneBy([
                    "id" => 1
                ]);
            } else {
                $etat = $etatRepository->findOneBy([
                    "id" => 2
                ]);
            }
            $sortie->setUser($this->getUser());
            $sortie->setSite($this->getUser()->getSite());
            $sortie->setEtat($etat);
            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this->redirectToRoute('app_sortie_index', ["id" => $sortie->getId()]);
        }
        return $this->render('sortie/new.html.twig', [
            "formSortie" => $formSortie->createView()
        ]);


    }

    #[Route('/{id}/edit', name: 'app_sortie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sortie $sortie,EtatRepository $etatRepository, SortieRepository $sortieRepository): Response
    {
        $formSortie = $this->createForm(SortieType::class, $sortie);
        $formSortie->handleRequest($request);


        if ($formSortie->isSubmitted() && $formSortie->isValid()) {
            //Il y a deux bouton différents, un pour enregistrer et l'autre pour publier
            //en fonction du bouton l'état de la sortie ne sera pas le même
            if ($formSortie->getClickedButton() === $formSortie->get('save')) {
                $etat = $etatRepository->findOneBy([
                    "id" => 1
                ]);
            } else {
                $etat = $etatRepository->findOneBy([
                    "id" => 2
                ]);
            }
            $sortie->setEtat($etat);
            $sortieRepository->save($sortie, true);

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'formSortie' => $formSortie,
        ]);
    }

    #[Route('/lieu', name: 'app_sortie_lieu', methods: ['GET', 'POST'])]
    public function addLieu(Request $request, LieuRepository $lieuRepository, EntityManagerInterface $entityManager): Response
    {
        $lieu = new Lieu();
        $formLieu = $this->createForm(LieuType::class, $lieu);
        $formLieu->handleRequest($request);

        if ($formLieu->isSubmitted() && $formLieu->isValid()) {
            $entityManager->persist($lieu);
            $entityManager->flush();
            return $this->redirectToRoute('app_sortie_new', ["id" => $lieu->getId()]);
        }
        return $this->render('sortie/new.lieu.html.twig', [
            "formLieu" => $formLieu->createView()
        ]);

    }

    #[Route('/afficher/{id}', name: 'app_sortie_show', methods: ['GET'])]
    public function show(Sortie $sortie): Response
    {
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_sortie_delete', methods: ['POST', 'GET'])]
    
    public function delete(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $sortie->getId(), $request->request->get('_token'))) {
            $sortieRepository->remove($sortie, true);
        }

        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/annuler/{id}', name: 'app_sortie_annuler', methods: ['POST', 'GET'])]
    public function annuler(Request $request, Sortie $sortie,EtatRepository $etatRepository, SortieRepository $sortieRepository): Response
    {
        $etat = $etatRepository->findOneBy([
            "id" => 6
            ]);
        $sortie->setEtat($etat);
        $sortieRepository->save($sortie, true);

        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/publier/{id}', name: 'app_sortie_publier', methods: ['POST', 'GET'])]
    public function publier(Request $request, Sortie $sortie,EtatRepository $etatRepository, SortieRepository $sortieRepository): Response
    {;

        $etat = $etatRepository->findOneBy([
            "id" => 2
        ]);
        $sortie->setEtat($etat);
        $sortieRepository->save($sortie, true);

        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/inscription/{id}', name: 'app_sortie_inscription', methods: ['GET'])]
    public function subscribe(
        Sortie                 $sortie,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ): Response
    {

        $participant =
            $entityManager->getRepository(User::class)->findOneBy(["email"=>$this->getUser()->getUserIdentifier()]);
        $sortie->addUsers($participant);
        $entityManager->persist($sortie);
        $entityManager->persist($participant);
        $entityManager->flush();

        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);

    }

    #[Route('/desinscription/{id}', name: 'app_sortie_desinscription', methods: ['GET'])]
    public function unsubscribe(
        Sortie                 $sortie,
        EntityManagerInterface $entityManager
    ): Response
    {
        $participant = $this->getUser();
        $sortie->removeUsers($participant);
        $entityManager->persist($sortie);
        $entityManager->persist($participant);
        $entityManager->flush();
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

}



