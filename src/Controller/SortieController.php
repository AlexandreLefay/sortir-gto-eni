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
        Request $request
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


//        Pour les filtres c'est un peu compliqué
        $search = new SearchData();
        $formSearch = $this->createForm(SearchFormType::class, $search,[
            'action' => $this->generateUrl('app_sortie_index'),
            'method' => 'POST',
            ]);

        $dateActuelle = new \DateTime("now");
        $dateActuelleString = $dateActuelle->format('Y-m-d H:i:s');

        return $this->render('sortie/index.html.twig', [
            'sorties' => $sortieRepository->findAll(), 'formSearch' =>$formSearch->createView(), 'dateNow' => $dateActuelleString
        ]);
    }

    #[Route('/new', name: 'app_sortie_new', methods: ['GET', 'POST'])]
    public function add(Request $request, UserRepository $user, EtatRepository $etatRepository, EntityManagerInterface $entityManager): Response
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
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ): Response
    {
        $participant = $userRepository->findOneBy(
            ['email'=>$this->getUser()->getUserIdentifier()]
        );
        $sortie->removeUsers($participant);
        $entityManager->persist($sortie);
        $entityManager->persist($participant);
        $entityManager->flush();
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

}



