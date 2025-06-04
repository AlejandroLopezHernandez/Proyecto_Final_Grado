<?php

namespace App\Controller;

use App\Enum\CategoriaComida;
use App\Repository\ComidaRepository;
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

    // Para mostrar todas las categorias existentes con o sin platos registrados
    #[Route('/comida/categorias', name: 'app_comida_categorias')]
    public function categorias(): Response
    {
        $categorias = CategoriaComida::cases();

        return $this->render('comida/cardTodasCategorias.html.twig', [
            'categorias' => $categorias
        ]);
    }

    // Para mostrar los platos que hay en cada categoria
    #[Route('/comida/categoria/{categoria}', name: 'app_comida_por_categoria')]
    public function comidasPorCategoria(string $categoria, ComidaRepository $comidaRepository): Response
    {
        $enumCategoria = CategoriaComida::from($categoria);
        $comidas = $comidaRepository->findByCategoria($enumCategoria);

        return $this->render('comida/comidas_por_categoria.html.twig', [
            'comidas' => $comidas,
            'categoria' => $categoria
        ]);
    }

    // Para mostrar los detalles de cada Comida
    #[Route('/comida/detalle/{id}', name: 'comida_detalle')]
    public function detalle(int $id, ComidaRepository $comidaRepository): Response
    {
        $comida = $comidaRepository->find($id);
    
        if (!$comida) {
            throw $this->createNotFoundException('Comida no encontrada');
        }
    
        $campos = [
            'Nombre' => $comida->getNombre(),
            'Precio' => $comida->getPrecio(),
            'Descripcion' => $comida->getDescripcion(),
            'Calorias' => $comida->getCalorias(),
            'Stock' => $comida->getStock(),
            'Categoria' => $comida->getCategoria()?->value ?? 'Sin categoría',
            'Proveedor' => $comida->getProveedor()
                ? '<a href="#">' . $comida->getProveedor()->getNombre() . '</a>'
                : 'Sin proveedor'
        ];
    
        return $this->render('cardDetalle.html.twig', [
            'campos' => $campos,
            'titulo' => $comida->getNombre()
        ]);
    }
    
}
