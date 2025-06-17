<?php

namespace App\Controller;

use App\Entity\Comida;
use App\Enum\CategoriaComida;
use App\Repository\ComidaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ComidaController extends AbstractController
{
    #[Route('/comida', name: 'comida_index')]
    public function index(ComidaRepository $comidaRepository): Response
    {
        $categorias = $comidaRepository->findCategoriasUnicas();

        return $this->render('panel/panelItems.html.twig', [
            'titulo' => 'Categorías de Comida',
            'elementos' => array_map(fn($cat) => ['value' => $cat], $categorias),
            'tipo' => 'categoria_comida',
        ]);
    }

    #[Route('/comida/categoriasJson', name: 'comida_categorias')]
    public function getCategorias(ComidaRepository $repositorio)
    {
        // Obtener las categorías de comida desde la base de datos
        $categorias = $repositorio->createQueryBuilder('c')
            ->select('DISTINCT c.categoria')
            ->getQuery()
            ->getResult();
        return new JsonResponse($categorias);
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
    #[Route('/comida/categoria/{categoria}', name: 'comida_por_categoria')]
    public function mostrarPorCategoria(string $categoria, ComidaRepository $comidaRepository): JsonResponse
    {
        $comidas = $comidaRepository->findByCategoria($categoria);

        $data = array_map(function ($comida) {
            return [
                'id' => $comida->getId(),
                'nombre' => $comida->getNombre(),
                'pvp' => $comida->getPvp(),
                'tipo' => 'comida'
            ];
        }, $comidas);

        return $this->json($data);
    }
    #[Route('/producto/{id}/opciones', name: 'producto_opciones')]
    public function obtenerOpciones(int $id, ComidaRepository $comidaRepository): JsonResponse
    {
        $opciones = $comidaRepository->findOpcionesByProductoId($id);

        // Convertir objeto clave:valor a array plano
        if (is_array($opciones) && array_values($opciones) !== $opciones) {
            $opciones = array_values($opciones);
        }

        return $this->json($opciones);
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
