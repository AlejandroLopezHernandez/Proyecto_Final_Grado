<?php

namespace App\Controller;

use App\Entity\Producto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductoController extends AbstractController
{
    #[Route('/producto', name: 'app_producto')]
    public function index(): Response
    {
        return $this->render('producto/index.html.twig', [
            'controller_name' => 'ProductoController',
        ]);
    }
    #[Route('/producto/detalle', name: 'productoInterfaz')]
    public function CardProduct(): Response
    {
        return $this->render('producto/plantillaProducto.html.twig');
    }
    #[Route('/producto/detalle/{id}', name: 'productoDetalle')]
    public function ProductoDetalle(Producto $producto): Response
    {
        return $this->render('producto/cardProductoDetalle.html.twig', [
            'producto' => $producto
        ]);
    }
}
