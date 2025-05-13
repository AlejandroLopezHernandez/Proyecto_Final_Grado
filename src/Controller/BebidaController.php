<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BebidaController extends AbstractController
{
    #[Route('/bebida', name: 'app_bebida')]
    public function index(): Response
    {
        return $this->render('bebida/index.html.twig', [
            'controller_name' => 'BebidaController',
        ]);
    }
}
