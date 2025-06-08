<?php

namespace App\Controller;

use App\Repository\BebidaRepository;
use App\Repository\ComidaRepository;
use App\Repository\EstiloRepository;
use App\Repository\FabricanteRepository;
use App\Repository\ProductoRepository;
use App\Repository\ProveedorRepository;
use App\Repository\ReservaRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;

final class EstadisticasController extends AbstractController
{
    private LoggerInterface $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    #[Route('/manager', name: 'DashboardManager')]
    public function index(
        BebidaRepository $bebidaRepository,
        ComidaRepository $comidaRepository,
        EstiloRepository $estiloRepository,
        FabricanteRepository $fabricanteRepository,
        ProductoRepository $productoRepository,
        ProveedorRepository $proveedorRepository,
        ReservaRepository $reservaRepository,
        Security $security
    ): Response {
        $usuario = $security->getUser();

        $datosCervezasXestilo = $bebidaRepository->cervezasXestilo();
        $datosBebidasXtipo = $bebidaRepository->contarBebidasPorTipo();
        $datosComidaXtipo = $comidaRepository->comidasXtipo();
        $datosComidaXprecio = $comidaRepository->comidasXprecio();
        $datosBebidaXprecio = $bebidaRepository->BebidasPorRangoPrecio();
        $datosProductosXproveedor = $productoRepository->contarProductosPorProveedor();

        $this->logger->info("El manager ha accecido al dashboard", [
            'usuario' => $usuario->getUserIdentifier(),
            'action' => 'check',
            'timestamp' => date('Y-m-d H:i:s')
        ]);

        return $this->render('estadisticas/index.html.twig', [
            'cervezasXestilo' => $datosCervezasXestilo,
            'bebidasXtipo' => $datosBebidasXtipo,
            'comidaXtipo' => $datosComidaXtipo,
            'comidaXprecio' => $datosComidaXprecio,
            'bebidaXprecio' => $datosBebidaXprecio,
            'productosXproveedor' => $datosProductosXproveedor,
        ]);
    }
    #[Route('/manager/cervezasXestilo', name: 'cervezasXestilo')]
    public function datosNcervezasXEstilo(BebidaRepository $bebidaRepository): JsonResponse
    {
        $datosNcervezasXEstilo = $bebidaRepository->cervezasXestilo();

        return $this->json($datosNcervezasXEstilo);
    }
    #[Route('/manager/nBebidasXtipo', name: 'nBebidasXtipo')]
    public function datosNbebidasXtipo(BebidaRepository $bebidaRepository): JsonResponse
    {
        $datosNbebidasXtipo = $bebidaRepository->contarBebidasPorTipo();

        return $this->json($datosNbebidasXtipo);
    }
    #[Route('/manager/NcomidasXtipo', name: 'NcomidasXtipo')]
    public function datosNComidaXtipo(ComidaRepository $comidaRepository): JsonResponse
    {
        $datosNComidaXtipo = $comidaRepository->comidasXtipo();

        return $this->json($datosNComidaXtipo);
    }
    #[Route('/manager/NcomidasXprecio', name: 'NcomidasXprecio')]
    public function datosNComidaXprecio(ComidaRepository $comidaRepository): JsonResponse
    {
        $datosNComidaXtipo = $comidaRepository->comidasXprecio();

        return $this->json($datosNComidaXtipo);
    }
    #[Route('/manager/NbebidasXprecio', name: 'NbebidasXprecio')]
    public function datosNbebidasXprecio(BebidaRepository $bebidaRepository): JsonResponse
    {

        $datosNbebidasXprecio = $bebidaRepository->BebidasPorRangoPrecio();

        return $this->json($datosNbebidasXprecio);
    }
    #[Route('/manager/productosXproveedor', name: 'productosXproveedor')]
    public function datosNproductosXproveedor(ProductoRepository $productoRepository): JsonResponse
    {
        $datosNproductosXproveedor = $productoRepository->contarProductosPorProveedor();

        return $this->json($datosNproductosXproveedor);
    }
}
