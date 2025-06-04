<?php

namespace App\Controller;

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
}
