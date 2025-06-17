<?php

namespace App\Controller;

use App\Entity\Estilo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EstiloController extends AbstractController
{
    #[Route('/estilo', name: 'app_estilo')]
    public function index(): Response
    {
        return $this->render('estilo/index.html.twig', [
            'controller_name' => 'EstiloController',
        ]);
    }
    #[Route('/estilo/{id}', name: 'app_ficha_estilo')]
    public function mostrar(Estilo $estilo): Response
    {
        return $this->render('estilo/index.html.twig', [
            'estilo' => $estilo,
        ]);
    }
}
