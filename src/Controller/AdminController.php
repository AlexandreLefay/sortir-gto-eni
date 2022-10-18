<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\NewUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin',methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $user = new User();
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
            $user->setActif(true);
            $user->setPassword(uniqid($prefix = "",$more_entropy=true));

            $entityManager->persist($user);
            $entityManager->flush();
        }
        return $this->render('admin/index.html.twig',[
            "formUser" => $formUser->createView()
        ]);
    }
}
