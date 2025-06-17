<?php

namespace App\Controller;

use App\Entity\Mesa;
use App\Repository\MesaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class MapaMesasController extends AbstractController
{
    #[Route('/mapa/editar', name: 'editar_mapa_mesas')]
    public function editarMapaMesas(MesaRepository $mesaRepository): Response
    {
        $mesas = $mesaRepository->findAll();
        return $this->render('mapa/editarMapaMesas.html.twig', [
            'mesas' => $mesas,
        ]);
    }

    #[Route('/mapa/guardar', name: 'guardar_mapa_mesas', methods: ['POST'])]
    public function guardarMapaMesas(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Elimina todas las mesas existentes
        $em->createQuery('DELETE FROM App\Entity\Mesa')->execute();

        // Añade nuevas mesas
        foreach ($data['mesas'] as $m) {
            $mesa = new Mesa();
            $mesa->setNombre($m['nombre']);
            $mesa->setPosicionX($m['posicionX']);
            $mesa->setPosicionY($m['posicionY']);
            $mesa->setActiva(true);
            $em->persist($mesa);
        }

        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/mapa/mostrar', name: 'mapa_mostrar')]
    public function mostrarMapa(MesaRepository $mesaRepo): JsonResponse
    {
        $mesas = $mesaRepo->findAll();
        $data = array_map(fn($mesa) => [
            'id' => $mesa->getId(),
            'nombre' => $mesa->getNombre(),
            'posicionX' => $mesa->getPosicionX(),
            'posicionY' => $mesa->getPosicionY(),
        ], $mesas);

        return $this->json(['mesas' => $data]);
    }
    // --- Borrar todas las mesas ---
    #[Route('/mapa/borrar-todas', name: 'mapa_borrar_todas', methods: ['POST'])]
    public function borrarTodasMesas(MesaRepository $mesaRepo, EntityManagerInterface $em): JsonResponse
    {
        // Usamos el método en repository:
        $deletedCount = $mesaRepo->deleteAllMesas();
        // Si prefieres, también podrías usar directamente DQL aquí, pero mejor la abstracción:
        // $deletedCount = $em->createQuery('DELETE FROM App\Entity\Mesa')->execute();
        return new JsonResponse([
            'success' => true,
            'deletedCount' => $deletedCount,
        ]);
    }

    // --- Borrar última mesa añadida ---
    #[Route('/mapa/borrar-ultima', name: 'mapa_borrar_ultima', methods: ['POST'])]
    public function borrarUltimaMesa(MesaRepository $mesaRepo, EntityManagerInterface $em): JsonResponse
    {
        $ultima = $mesaRepo->findUltimaMesa();
        if (!$ultima) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No hay mesas para eliminar'
            ]);
        }
        $idEliminada = $ultima->getId();
        $em->remove($ultima);
        $em->flush();
        return new JsonResponse([
            'success' => true,
            'deletedId' => $idEliminada,
        ]);
    }
}
