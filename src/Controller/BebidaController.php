<?php

namespace App\Controller;

use App\Entity\Bebida;
use App\Repository\BebidaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class BebidaController extends AbstractController
{
    #[Route('/bebida', name: 'bebida_panel')]
    public function index(BebidaRepository $bebidaRepository): Response
    {
        $categorias = $bebidaRepository->findTiposBebida();

        return $this->render('panel/panelItems.html.twig', [
            'titulo' => 'Categorías de Bebida',
            'elementos' => array_map(fn($cat) => ['value' => $cat], $categorias),
            'tipo' => 'categoria_bebida',
        ]);
    }

    #[Route('/bebida/tipos', name: 'bebida_tipos')]
    public function obtenerTipos(BebidaRepository $repo): JsonResponse
    {
        return $this->json($repo->findTiposBebida());
    }

    #[Route('/bebida/{tipo}/registros', name: 'bebida_por_tipo')]
    public function bebidasPorTipo(BebidaRepository $repo, string $tipo): JsonResponse
    {
        $bebidas = $repo->findBebidasPorTipo($tipo);

        return $this->json(array_map(function ($b) {
            return [
                'id' => $b->getId(),
                'nombre' => $b->getNombre(),
                'pvp' => $b->getPvp(),
                'formato' => $b->getFormato(),
                // lo que necesites mostrar
            ];
        }, $bebidas));
    }


    #[Route('/bebida/{tipo}/estilos', name: 'bebida_estilos')]
    public function estilosPorTipo(BebidaRepository $repo, string $tipo): JsonResponse
    {
        $estilos = $repo->findEstilosPorTipo($tipo);
        return $this->json($estilos);
    }

    #[Route('/bebida/{tipo}/{estilo}/registros', name: 'bebidas_por_estilo')]
    public function bebidasPorEstilo(BebidaRepository $repo, string $tipo, string $estilo): JsonResponse
    {
        $bebidas = $repo->findBebidasPorEstiloYTipo($tipo, $estilo); // CORRECTO orden

        return $this->json(array_map(function ($b) {
            return [
                'id' => $b->getId(),
                'nombre' => $b->getNombre(),
                'pvp' => $b->getPvp(),
                'formato' => $b->getFormato(),
            ];
        }, $bebidas));
    }
}
