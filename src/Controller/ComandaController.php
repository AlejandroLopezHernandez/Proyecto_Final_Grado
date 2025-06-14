<?php

namespace App\Controller;

use App\Entity\Comanda;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ComandaController extends AbstractController
{
    #[Route('/comanda/guardar', name: 'comanda_guardar', methods: ['POST'])]
    public function guardar(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data || !isset($data['productos'])) {
            return new JsonResponse(['success' => false, 'error' => 'Datos inválidos'], 400);
        }

        // Crear y persistir Comanda
        $comanda = new Comanda();
        // Guardamos directamente el array recibido en 'productos'
        $comanda->setProductos($data['productos']);
        // Fecha: si se envía desde cliente en data['fecha'], opcionalmente usarlo; 
        // aquí usamos server time para consistencia:
        $comanda->setFecha(new \DateTime());

        $em->persist($comanda);
        $em->flush();

        return new JsonResponse(['success' => true, 'id' => $comanda->getId()]);
    }
}

