<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]

    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/home/accueil', name: 'app_accueil')]
    public function accueil(): Response
    {
        return $this->render('/home/accueil.html.twig', [
        ]);
    }

    //Villes et sites uniquement accessible à l'Admin
    #[Route('/home/villes', name: 'app_villes')]
    public function villes(): Response
    {
        return $this->render('/home/villes.html.twig', [
        ]);
    }
    #[Route('/home/sites', name: 'app_sites')]
    public function sites(): Response
    {
        return $this->render('/home/sites.html.twig', [
        ]);
    }
}
