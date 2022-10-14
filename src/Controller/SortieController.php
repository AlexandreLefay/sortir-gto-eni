<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\SearchData;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\LieuType;
use App\Form\SearchFormType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'app_sortie_index', methods: ['GET', 'POST'])]

    public function index(SortieRepository $sortieRepository, EtatRepository $etatRepository,  EntityManagerInterface $entityManager): Response
    {
        $sortie = new sortie();
        $sorties =  $sortieRepository->findAll();
        $dateActuelle = new \DateTime("now");
        $dateActuelle = $dateActuelle->format('Y-m-d H:i:s');

        foreach ($sorties as $sortie ) {
            date_default_timezone_set('Europe/Paris');
            $dateDebut = $sortie->getDateDebut()->format('Y-m-d H:i:s');
            $dateCloture = $sortie->getDateCloture()->format('Y-m-d H:i:s');
            $heureDuree = $sortie->getDuree();
            $dateFin = date('Y-m-d H:i:s',strtotime($heureDuree.' day',strtotime($dateDebut)));
            $etatActuel = $sortie->getEtat()->getId();



//            SI LA DATE DE CLOTURE EST ARRIVE OU QUE LE NOOMBRE DE PARTICIPANT MAX EST ATTEINT ETAT = CLOTUREE
            if ($dateCloture <= $dateActuelle) {

                $etat = $etatRepository->findOneBy([
                    "id" => 3
                ]);
                $sortie->setEtat($etat);
                $entityManager->persist($sortie);
                $entityManager->flush();
            }

//            SI LA SORTIE EST A L'ETAT PUBLIE(OUVERTE) OU PUBLIE(CLOTUREE),  A COMMENCE ET N'EST PAS TERMINE  ETAT = ACT EN COURS'
            if($dateActuelle > $dateDebut && $dateActuelle < $dateFin && $etatActuel == 2 || $etatActuel == 3) {

                $etat = $etatRepository->findOneBy([
                    "id" => 4
                ]);
                $sortie->setEtat($etat);
                $entityManager->persist($sortie);
                $entityManager->flush();
            }

//          SI LA DATE DE FIN DE SORTIE EST INFERIEUR A LA DATE ACTUELLE ET QUE LA SORTIE ETAIT EN COURS ALORS ETAT = PASSE
            if ($dateCloture > $dateFin && $etatActuel == 4 ) {

                $etat = $etatRepository->findOneBy([
                    "id" => 5
                ]);
                $sortie->setEtat($etat);
                $entityManager->persist($sortie);
                $entityManager->flush();
            }

//            SI LA DATE DE FIN DE SORTTIE EST SUPERIEUR A 1 MONTH ET QUE L'ETAT ACTUEL  EST PASSE ALORS ETAT = ARCHIVE
            $dateFin = strtotime($dateFin);
            $dateActuelle = strtotime($dateActuelle);
            $jourTotalArchive = ($dateActuelle-$dateFin)/86400;

            if ($jourTotalArchive > 30 && $etatActuel == 5 ) {
                $etat = $etatRepository->findOneBy([
                    "id" => 7
                ]);
                $sortie->setEtat($etat);
                $entityManager->persist($sortie);
                $entityManager->flush();
            }
        }
        
        $search = new SearchData();
        $formSearch = $this->createForm(SearchFormType::class, $search,[
            'action' => $this->generateUrl('app_sortie_index'),
            'method' => 'POST',
            ]);

        return $this->render('sortie/index.html.twig', [
            'sorties' => $sortieRepository->findAll(), 'formSearch' =>$formSearch->createView()
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
        EntityManagerInterface $entityManager
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
