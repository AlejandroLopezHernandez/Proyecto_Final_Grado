<?php

namespace App\Controller;

use App\Entity\Reserva;
use App\Enum\EstadoReserva;
use App\Form\ReservaType;
use App\Repository\ReservaRepository;
use App\Service\ReservaService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservaController extends AbstractController
{
    private LoggerInterface $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    #[Route('/reservas', name: 'app_reservas')]
    public function index(ReservaRepository $reservaRepository): Response
    {
        $reservas = $reservaRepository->findAll();
        return $this->render('reserva/listaReserva.html.twig', [
            'reservas' => $reservas,
        ]);
    }

    #[Route('/reservas/nueva', name: 'app_nueva_reserva')]
    public function nueva(Request $request, ReservaService $reservaService): Response
    {
        $reserva = new Reserva();
        $reserva->setEstado(EstadoReserva::Pendiente);

        $form = $this->createForm(ReservaType::class, $reserva);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $exito = $reservaService->crearReserva($reserva);

            $this->logger->info('Nueva reserva creada', [
                'nombre' => $reserva->getNombreCliente(),
                'email' => $reserva->getEmailCliente(),
                'fecha' => $reserva->getFechaHoraReserva()?->format('Y-m-d H:i'),
                'mesa' => $reserva->getNumeroMesa(),
                'ip' => $request->getClientIp(),
            ]);

            if ($exito) {
                $this->addFlash('success', '¡Reserva demandada con éxito!Espere mail de confirmación');
                return $this->redirectToRoute('app_reservas');
            } else {
                $this->addFlash('error', 'No hay disponibilidad para esa fecha y hora según Google Reservas.');
            }
        }

        return $this->render('reserva/formularioNuevaReserva.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reservas/{id}/cancelar', name: 'app_cancelar_reserva')]
    public function cancelar(Reserva $reserva, ReservaService $reservaService): Response
    {
        $reservaService->cancelarReserva($reserva);
        $this->addFlash('success', 'Reserva cancelada correctamente.');

        return $this->redirectToRoute('app_reservas');
    }
}
