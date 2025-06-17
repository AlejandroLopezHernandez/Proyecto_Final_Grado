<?php

namespace App\Controller;

use App\Repository\ComidaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BebidaRepository;


class CartaDinamicaController extends AbstractController
{
    #[Route('/cartaCliente', name: 'carta_cliente')]
    public function index(ComidaRepository $comidaRepository, BebidaRepository $bebidaRepository): Response
    {
        $categorias = \App\Enum\CategoriaComida::cases();
        $tiposBebida = \App\Enum\TipoBebida::cases();
        $formatoBebida = \App\Enum\FormatoBebida::cases();

        $comidas = $comidaRepository->findAll();
        $bebidas = $bebidaRepository->findAll();

        return $this->render('carta/cartaCliente.html.twig', [
            'comidas' => $comidas,
            'categorias' => $categorias,
            'tiposBebida' => $tiposBebida,
            'formatoBebida' => $formatoBebida,
            'bebidas' => $bebidas
        ]);
    }
}
