<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ComidaController extends AbstractController
{
    #[Route('/comida', name: 'app_comida')]
    public function index(): Response
    {
        return $this->render('comida/index.html.twig', [
            'controller_name' => 'ComidaController',
        ]);
    }
}
