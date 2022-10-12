<?php

namespace App\Controller;

use App\Form\UserProfilType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil_update', name: 'app_profil_update')]
    public function profil_update(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ): Response
    {
        $userNew = $userRepository->findOneBy(
            ['email'=>$this->getUser()->getUserIdentifier()]
        );
        $formUser = $this->createForm(UserProfilType::class, $userNew);
        $formUser->handleRequest($request);

        if($formUser->isSubmitted() && $formUser->isValid()){
            $entityManager->persist($userNew);
            $entityManager->flush();
        }

        return $this->renderForm('profil/profilUpdate.html.twig',compact('formUser'));
    }

    #[Route('/profil_view', name: 'app_profil_view')]
    public function index(
        UserRepository $userRepository
    ): Response
    {
        $user = $userRepository->findOneBy(
            ['email'=>$this->getUser()->getUserIdentifier()]
        );

        return $this->render('profil/profilView.html.twig',compact('user'));
    }
}
