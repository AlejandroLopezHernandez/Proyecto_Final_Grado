<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanelProductoController extends AbstractController
{
    #[Route('/panel/productos', name: 'panel_productos')]
    public function index(): Response
    {
        return $this->render('panel/moduloProducto.html.twig');
    }

    #[Route('/cargar/modulo-productos', name: 'cargar_modulo_productos')]
    public function cargarModuloProductos(): Response
    {
        return $this->render('panel/moduloProducto.html.twig', [
            'sin_layout' => true,
        ]);
    }

    
}
