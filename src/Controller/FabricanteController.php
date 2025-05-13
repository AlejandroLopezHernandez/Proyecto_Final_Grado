<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FabricanteController extends AbstractController
{
    #[Route('/fabricante', name: 'app_fabricante')]
    public function index(): Response
    {
        return $this->render('fabricante/index.html.twig', [
            'controller_name' => 'FabricanteController',
        ]);
    }
}
