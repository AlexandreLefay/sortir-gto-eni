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
    #[Route('/profil', name: 'app_profil')]
    public function index(
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

        return $this->renderForm('profil/index.html.twig',compact('formUser'));
    }
}
