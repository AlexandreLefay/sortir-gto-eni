<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    #[Route('/sortie', name: 'app_sortie')]
    public function index(
        SortieRepository $SortieRepository,

    ): Response
    {
        $sorties = $SortieRepository->findAll();
        return $this->render('/sortie/afficher-sortie.html.twig', [
            "sorties" => $sorties
        ]);
    }
}
