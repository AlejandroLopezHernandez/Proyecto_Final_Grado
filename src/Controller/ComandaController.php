<?php

namespace App\Controller;

use App\Entity\Comanda;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ComandaRepository;
use Symfony\Component\HttpFoundation\Response;

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

    #[Route('/comanda/ver', name: 'comanda_ver')]
    public function verComandas(ComandaRepository $repo): Response
    {
        // Puedes usar el método del repo o directamente findBy
        $comandas = $repo->findBy([], ['fecha' => 'DESC']);

        return $this->render('comanda/verComanda.html.twig', [
            'comandas' => $comandas,
        ]);
    }
    #[Route('/comanda/imprimir/{id}', name: 'comanda_imprimir')]
    public function imprimirComanda(int $id, EntityManagerInterface $em): Response
    {
        $comanda = $em->getRepository(Comanda::class)->find($id);
        if (!$comanda) {
            throw $this->createNotFoundException('Comanda no encontrada');
        }

        $html = $this->renderView('comanda/ticket_pdf.html.twig', [
            'comanda' => $comanda,
        ]);

        // Generar PDF con Dompdf
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();

        // Guardar en public/tickets
        $output = $dompdf->output();
        $filename = 'ticket_' . $id . '.pdf';
        $path = $this->getParameter('kernel.project_dir') . '/public/tickets/' . $filename;

        file_put_contents($path, $output);

        return $this->json([
            'success' => true,
            'url' => '/tickets/' . $filename
        ]);
    }
}
