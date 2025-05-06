<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProveedorController extends AbstractController
{
    #[Route('/proveedor', name: 'app_proveedor')]
    public function index(): Response
    {
        return $this->render('proveedor/index.html.twig', [
            'controller_name' => 'ProveedorController',
        ]);
    }
}
