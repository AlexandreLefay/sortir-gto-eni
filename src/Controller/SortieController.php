<?php

namespace App\Controller;

//use App\EtatUpdate;
use App\Entity\User;
use App\Entity\Lieu;
use App\Entity\SearchData;
use App\Entity\Sortie;
use App\EtatUpdate\EtatUpdateFunction;
use App\Form\LieuType;
use App\Form\SearchFormType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
//use Container8wOy52z\get_ServiceLocator_ZFcJjKUService;
use Doctrine\ORM\EntityManagerInterface;
//use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//use Symfony\Flex\Event\UpdateEvent;

#[Route('/sortie')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'app_sortie_index', methods: ['GET', 'POST'])]
    public function index(
        SortieRepository $sortieRepository,
        Request          $request,
        UserRepository   $userRepository
    ): Response

    {
//     création cookie, avec mail si REMEMBERME coché pour affichage lors reconnexion
        $res = new Response();
        $res->headers->clearCookie('mail');
        $res->send();

        if ($request->cookies->get('REMEMBERME')) {
            $cookie = new Cookie ('mail', $this->getUser()->getUserIdentifier());
            $res = new Response();
            $res->headers->setCookie($cookie);
            $res->send();
        }

        $user = $userRepository->findOneBy(
            ['email'=>$this->getUser()->getUserIdentifier()]
        );
        if($user->getNom()==null ||$user->getPrenom()==null ||$user->getTelephone()==null){
            return $this->redirectToRoute('app_profil_update');
        }

//        Pour les filtres c'est un peu compliqué
        $search = new SearchData();
        $formSearch = $this->createForm(SearchFormType::class, $search, [
            'action' => $this->generateUrl('app_sortie_index'),
            'method' => 'POST',
        ]);
        $dateActuelle = new \DateTime("now");
        $dateActuelleString = $dateActuelle->format('Y-m-d H:i:s');
        $userConnected = $this->getUser()->getId();
        $siteId = $this->getUser()->getSite()->getId();
        $formSearch->handleRequest($request);
        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $orgaCheckbox = $search->getOrganisateur();
            $mesSortiesCheckbox = $search->getInscrit();
            $nonInscritCheckbox = $search->getNonInscrit();
            $sortiesFiniesCheckbox = $search->getPassees();
            $dateSortieDebut = $search->getDateSortieDebut();
            $dateSortieFin = $search->getDateSortieFin();
            $searchbar = $search->getSearchbar();
            $siteId = $search->getSite()->getId();
            return $this->render('sortie/index.html.twig', [
                'sorties' => $sortieRepository->queryfilter($userConnected,$orgaCheckbox,$nonInscritCheckbox,$mesSortiesCheckbox,$searchbar,$sortiesFiniesCheckbox,$dateSortieDebut,$dateSortieFin,$siteId), 'formSearch' => $formSearch->createView(), 'dateNow' => $dateActuelleString
            ]);
        }
//       dd($sortieRepository->findSiteId($siteId));
        return $this->render('sortie/index.html.twig', [
            'sorties' => $sortieRepository->findSiteId($siteId), 'formSearch' => $formSearch->createView(), 'dateNow' => $dateActuelleString
        ]);
    }

    #[Route('/new', name: 'app_sortie_new', methods: ['GET', 'POST'])]
    public function add(Request $request, UserRepository $user, EtatRepository $etatRepository, EntityManagerInterface $entityManager): Response
    {
        $sortie = new Sortie();
        $formSortie = $this->createForm(SortieType::class, $sortie);
        $formSortie->handleRequest($request);

        if ($formSortie->isSubmitted() && $formSortie->isValid()) {
            //Il y a deux boutons différents, un pour enregistrer et l'autre pour publier
            //en fonction du bouton l'état de la sortie ne sera pas le même.
            if ($formSortie->getClickedButton() === $formSortie->get('save')) {
                $etat = $etatRepository->findOneBy([
                    "id" => 1
                ]);
            } else {
                $etat = $etatRepository->findOneBy([
                    "id" => 2
                ]);
            }
//            dd($formSortie->getData()->getDateDebut());
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
    public function edit(Request $request, Sortie $sortie, EtatRepository $etatRepository, SortieRepository $sortieRepository): Response
    {
        $formSortie = $this->createForm(SortieType::class, $sortie);
        $formSortie->handleRequest($request);


        if ($formSortie->isSubmitted() && $formSortie->isValid()) {
            //Il y a deux boutons différents, un pour enregistrer et l'autre pour publier
            //en fonction du bouton l'état de la sortie ne sera pas le même.
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
    public function annuler(
        Request $request,
        Sortie $sortie,
        EtatRepository $etatRepository,
        SortieRepository $sortieRepository,
        $data = array(
            'text' => '',
        )
    ): Response
    {
        $formAnnulation = $this->createFormBuilder($data)
            ->add('text', TextType::class)
            ->add('save', SubmitType::class)
            ->getForm();
        $formAnnulation->handleRequest($request);

        if ($formAnnulation->getClickedButton() === $formAnnulation->get('save')) {
            $etat = $etatRepository->findOneBy([
                "id" => 6
            ]);
//            dd($formAnnulation->getData()["text"]);
            $sortie->setEtat($etat);
            $sortie->setDescriptionsInfos($formAnnulation->getData()["text"]);
            $sortieRepository->save($sortie, true);

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }



        return $this->render('sortie/annulation.html.twig', [
            "formAnnulation" => $formAnnulation->createView(),
            "sortie" => $sortie
        ]);
    }

    #[Route('/publier/{id}', name: 'app_sortie_publier', methods: ['POST', 'GET'])]
    public function publier(Request $request, Sortie $sortie, EtatRepository $etatRepository, SortieRepository $sortieRepository): Response
    {
        ;

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
        UserRepository         $userRepository,
        EtatRepository         $etatRepository,
        EtatUpdateFunction $updateEvent
    ): Response
    {

//        dd('$nbrUsersInscrit '.$nbrUsersInscrit.'$nbrInscritMax '.$nbrInscritMax);
        $participant = $entityManager->getRepository(User::class)->findOneBy(["email" => $this->getUser()->getUserIdentifier()]);
        $sortie->addUsers($participant);
        $entityManager->persist($sortie);
//        $entityManager->persist($participant);
        $entityManager->flush();

        $Users = $sortie->getUsers();
        $nbrUsersInscrit = count($Users);
        $nbrInscritMax = $sortie->getNbInscriptionsMax();

        if ($nbrUsersInscrit == $nbrInscritMax) {
            $etat = $etatRepository->findOneBy([
                "id" => 3
            ]);
            $updateEvent->updateEtatFlush($etat, $sortie);
        }

        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);

    }

    #[Route('/desinscription/{id}', name: 'app_sortie_desinscription', methods: ['GET'])]
    public function unsubscribe(
        Sortie                 $sortie,
        EntityManagerInterface $entityManager,
        UserRepository         $userRepository,
        EtatUpdateFunction     $updateEvent,
        EtatRepository         $etatRepository,
    ): Response
    {
        $participant = $userRepository->findOneBy(
            ['email' => $this->getUser()->getUserIdentifier()]
        );
        $sortie->removeUsers($participant);
        $entityManager->persist($sortie);
        $entityManager->persist($participant);
        $entityManager->flush();

        $Users = $sortie->getUsers();
        $nbrUsersInscrit = count($Users);
        $nbrInscritMax = $sortie->getNbInscriptionsMax();

        if ($nbrUsersInscrit < $nbrInscritMax) {
            $etat = $etatRepository->findOneBy([
                "id" => 2
            ]);
            $updateEvent->updateEtatFlush($etat, $sortie);
        }
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

}



