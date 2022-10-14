<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserProfilType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfilController extends AbstractController
{
    #[Route('/profil_update', name: 'app_profil_update')]
    public function profil_update(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response
    {
        $userNew = $this->getUser();
        $formUser = $this->createForm(UserProfilType::class, $userNew);
        $formUser->handleRequest($request);

        if($formUser->isSubmitted() && $formUser->isValid()){
            $image = $formUser->get('image')->getData();

            if($image){
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessClientExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
                $userNew->setPhoto('uploads/'.$newFilename);
            }
            $entityManager->persist($userNew);
            $entityManager->flush();
        }

        return $this->renderForm('profil/profilUpdate.html.twig',compact('formUser'));
    }

    #[Route('/profil_view/{id}', name: 'app_profil_view')]
    public function index(
        UserRepository $userRepository,
        User $user
    ): Response
    {
        return $this->render('profil/profilView.html.twig',compact('user'));
    }
}
