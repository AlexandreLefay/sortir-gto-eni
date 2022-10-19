<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\NewUserType;
use App\Repository\UserRepository;
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
        UserRepository $userRepository
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
            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();
        }
        $users = $userRepository->findAll();
        return $this->renderForm('admin/index.html.twig',compact('formUser','users'));
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
}
