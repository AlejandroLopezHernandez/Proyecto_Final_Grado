<?php

namespace App\Controller;

use App\Entity\ProductoComida;
use App\Form\ProductoComidaType;
use App\Repository\ProductoComidaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/producto/comida')]
final class ProductoComidaController extends AbstractController
{
    #[Route(name: 'app_producto_comida_index', methods: ['GET'])]
    public function index(ProductoComidaRepository $productoComidaRepository): Response
    {
        return $this->render('producto_comida/index.html.twig', [
            'producto_comidas' => $productoComidaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_producto_comida_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $productoComida = new ProductoComida();
        $form = $this->createForm(ProductoComidaType::class, $productoComida);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($productoComida);
            $entityManager->flush();

            return $this->redirectToRoute('app_producto_comida_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('producto_comida/new.html.twig', [
            'producto_comida' => $productoComida,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_producto_comida_show', methods: ['GET'])]
    public function show(ProductoComida $productoComida): Response
    {
        return $this->render('producto_comida/show.html.twig', [
            'producto_comida' => $productoComida,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_producto_comida_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProductoComida $productoComida, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductoComidaType::class, $productoComida);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_producto_comida_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('producto_comida/edit.html.twig', [
            'producto_comida' => $productoComida,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_producto_comida_delete', methods: ['POST'])]
    public function delete(Request $request, ProductoComida $productoComida, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$productoComida->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($productoComida);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_producto_comida_index', [], Response::HTTP_SEE_OTHER);
    }
}
