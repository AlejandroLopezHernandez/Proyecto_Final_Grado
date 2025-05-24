<?php

namespace App\Controller;

use App\Entity\Reserva;
use App\Enum\EstadoReserva;
use App\Form\ReservaType;
use App\Repository\ReservaRepository;
use App\Service\ReservaService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservaController extends AbstractController
{
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

            if ($exito) {
                $this->addFlash('success', '¡Reserva confirmada con éxito! Google Reservas ha asignado la mesa #' . $reserva->getNumeroMesa());
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
