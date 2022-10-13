<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\SearchData;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\LieuType;
use App\Form\SearchFormType;
use App\Form\SortieLieuType;
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
    #[Route('/', name: 'app_sortie_index', methods: ['GET'])]
    public function index(SortieRepository $sortieRepository): Response
    {
        return $this->render('sortie/index.html.twig', [
            'sorties' => $sortieRepository->findAll(),
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

    #[Route('/{id}', name: 'app_sortie_delete', methods: ['POST'])]
    public function delete(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $sortie->getId(), $request->request->get('_token'))) {
            $sortieRepository->remove($sortie, true);
        }

        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/inscription/{id}', name: 'app_sortie_inscription', methods: ['GET'])]
    public function subscribe(
        Sortie                 $sortie,
        EntityManagerInterface $entityManager
    ): Response
    {
        $participant = $this->getUser();
        $sortie->addUsers($participant);
        $entityManager->persist($sortie);
        $entityManager->flush($sortie);
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
        $entityManager->flush($sortie);
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    #[Route('/search', name: 'app_sortie_search', methods: ['GET'])]
    public function search(SortieRepository $sortieRepository): Response
    {
        $search = new SearchData();
        $formSearch = $this->createForm(SearchFormType::class, $search);
        $formSearch>handleRequest($search);
        return $this->render('sortie/index.html.twig', [
            'sorties' => $sortieRepository->findAll(),
        ]);
    }
}
