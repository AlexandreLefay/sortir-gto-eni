<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\User;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Form\NewUserType;
use App\Form\SiteFormType;
use App\Form\VilleFormType;
use App\Repository\LieuRepository;
use App\Repository\SiteRepository;
use App\Repository\UserRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin',methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        UserRepository $userRepository,
        LieuRepository $lieuRepository,
        SiteRepository $siteRepository,
        VilleRepository $villeRepository
    ): Response
    {
        $user = new User();
        $lieu = new Lieu();
        $site = new Site();
        $ville = new Ville();
        $user->setRoles(["ROLE_USER"]);

        $formUser = $this->createForm(NewUserType::class,$user,[
            'method'=>'POST'
        ]);
        $formUser->handleRequest($request);
        if($formUser->isSubmitted() && $formUser->isValid()){
            if($user->getAdministrateur()){
                $user->setRoles(["ROLE_ADMIN"]);
            };
            $user->setPseudo(uniqid());
            $user->setPhoto('uploads/defaut_image.jpeg');
            $user->setActif(true);
            $user->setPassword(uniqid($prefix = "",$more_entropy=true));
            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();
        }

        $formLieu = $this->createForm(LieuType::class,$lieu,[
            'method'=>'POST'
        ]);
        $formLieu->handleRequest($request);
        if($formLieu->isSubmitted() && $formLieu->isValid()){
            $entityManagerInterface->persist($lieu);
            $entityManagerInterface->flush();
        }

        $formSite = $this->createForm(SiteFormType::class,$site,[
            'method'=>'POST'
        ]);
        $formSite->handleRequest($request);
        if($formSite->isSubmitted() && $formSite->isValid()){
            $entityManagerInterface->persist($site);
            $entityManagerInterface->flush();
        }
        $formVille = $this->createForm(VilleFormType::class,$ville,[
            'method'=>'POST'
        ]);
        $formVille->handleRequest($request);
        if($formVille->isSubmitted() && $formVille->isValid()){
            $entityManagerInterface->persist($ville);
            $entityManagerInterface->flush();
        }

        $users = $userRepository->findAll();
        $lieux = $lieuRepository->findAll();
        $sites = $siteRepository->findAll();
        $villes = $villeRepository->findAll();
        return $this->renderForm('admin/index.html.twig',compact(
            'formUser','users','lieux','sites','formSite','formLieu','formVille','villes'
        ));
    }

    #[Route('/desactiveUser/{id}', name: 'app_admin_desactiveUser',methods: ['GET', 'POST'])]
    public function desactiveUser(
        User $user,
        EntityManagerInterface $entityManagerInterface
    ): Response
    {
        $user->setActif(!$user->getActif());
        $entityManagerInterface->persist($user);
        $entityManagerInterface->flush();
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/deleteUser/{id}', name: 'app_admin_deleteUser',methods: ['GET', 'POST'])]
    public function deleteUser(
        User $user,
        EntityManagerInterface $entityManagerInterface
    ): Response
    {
        $entityManagerInterface->remove($user);
        $entityManagerInterface->flush();
        return $this->redirectToRoute('app_admin');
    }
    #[Route('/deleteLieu/{id}', name: 'app_admin_deleteLieu',methods: ['GET', 'POST'])]
    public function deleteLieu(
        Lieu $lieu,
        EntityManagerInterface $entityManagerInterface
    ): Response
    {
        $entityManagerInterface->remove($lieu);
        $entityManagerInterface->flush();
        return $this->redirectToRoute('app_admin');
    }
    #[Route('/deleteSite/{id}', name: 'app_admin_deleteSite',methods: ['GET', 'POST'])]
    public function deleteSite(
        Site $site,
        EntityManagerInterface $entityManagerInterface
    ): Response
    {
        $entityManagerInterface->remove($site);
        $entityManagerInterface->flush();
        return $this->redirectToRoute('app_admin');
    }
    #[Route('/deleteVille/{id}', name: 'app_admin_deleteVille',methods: ['GET', 'POST'])]
    public function deleteVille(
        Ville $ville,
        EntityManagerInterface $entityManagerInterface
    ): Response
    {
        $entityManagerInterface->remove($ville);
        $entityManagerInterface->flush();
        return $this->redirectToRoute('app_admin');
    }

}
