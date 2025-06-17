<?php

namespace App\Controller;

use App\Repository\BebidaRepository;
use App\Repository\ComandaRepository;
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
        ProductoRepository $productoRepository,
        ComandaRepository $comandaRepository,
        Security $security
    ): Response {
        $usuario = $security->getUser();

        $datosCervezasXestilo = $bebidaRepository->cervezasXestilo();
        $datosBebidasXtipo = $bebidaRepository->contarBebidasPorTipo();
        $datosComidaXtipo = $comidaRepository->comidasXtipo();
        $datosComidaXprecio = $comidaRepository->comidasXprecio();
        $datosBebidaXprecio = $bebidaRepository->BebidasPorRangoPrecio();
        $datosProductosXproveedor = $productoRepository->contarProductosPorProveedor();
        $cervezasMasVendidas = $comandaRepository->getCervezasMasVendidas();
        $BebidasMasVendidas = $comandaRepository->getBebidasMasVendidas();
        $ComidasMasVendidas = $comandaRepository->getComidasMasVendidas();
        $RefrescosMasVendidos = $comandaRepository->getRefrescosMasVendidos();
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
            'cervezas_mas_vendidas' => $cervezasMasVendidas,
            'bebidas_mas_vendidas' => $BebidasMasVendidas,
            'comidas_mas_vendidas' => $ComidasMasVendidas,
            'refrescos_mas_vendidos' => $RefrescosMasVendidos
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
    #[Route('/manager/cervezaMasVendida', name: 'cervezaMasVendida')]
    public function cervezaMasVendida(ComandaRepository $comandaRepository): JsonResponse
    {
        $cervezasMasVendidas = $comandaRepository->getCervezasMasVendidas();
        return $this->json($cervezasMasVendidas);
    }
    #[Route('/manager/ComidaMasVendida', name: 'ComidaMasVendida')]
    public function ComidaMasVendida(ComandaRepository $comandaRepository): JsonResponse
    {
        $ComidaMasVendidas = $comandaRepository->getComidasMasVendidas();
        return $this->json($ComidaMasVendidas);
    }
    #[Route('/manager/BebidaMasVendida', name: 'BebidaMasVendida')]
    public function BebidaMasVendida(ComandaRepository $comandaRepository): JsonResponse
    {
        $BebidaMasVendidas = $comandaRepository->getBebidasMasVendidas();
        return $this->json($BebidaMasVendidas);
    }
    #[Route('/manager/RefrescosMasVendida', name: 'RefrescosMasVendida')]
    public function RefrescosMasVendida(ComandaRepository $comandaRepository): JsonResponse
    {
        $RefrescosMasVendidas = $comandaRepository->getRefrescosMasVendidos();
        return $this->json($RefrescosMasVendidas);
    }
}
