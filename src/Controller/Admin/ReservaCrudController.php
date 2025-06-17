<?php

namespace App\Controller\Admin;

use App\Entity\Reserva;
use App\Enum\EstadoReserva;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class ReservaCrudController extends AbstractCrudController
{
    private AdminUrlGenerator $adminUrlGenerator;
    private $logger;
    private $security;
    private MailerInterface $mailer;
    public function __construct(MailerInterface $mailer, AdminUrlGenerator $adminUrlGenerator, LoggerInterface $logger, Security $security)
    {
        $this->mailer = $mailer;
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->logger = $logger;
        $this->security = $security;
    }
    public static function getEntityFqcn(): string
    {
        return Reserva::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Reserva')
            ->setEntityLabelInPlural('Reservas')
            ->setSearchFields(['nombreCliente', 'emailCliente', 'telefonoCliente'])
            ->setDefaultSort(['fechaHoraReserva' => 'DESC'])
            ->setPaginatorPageSize(25)
            ->showEntityActionsInlined();
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),

            TextField::new('nombreCliente')
                ->setLabel('Nombre del Cliente')
                ->setRequired(true)
                ->setColumns(6),

            EmailField::new('emailCliente')
                ->setLabel('Email')
                ->setRequired(true)
                ->setColumns(6),

            TelephoneField::new('telefonoCliente')
                ->setLabel('Teléfono')
                ->setColumns(6),

            DateTimeField::new('fechaHoraReserva')
                ->setLabel('Fecha y Hora de Reserva')
                ->setRequired(true)
                ->setColumns(6),

            IntegerField::new('numeroComensales')
                ->setLabel('Número de Comensales')
                ->setRequired(true)
                ->setColumns(4),

            IntegerField::new('numeroMesa')
                ->setLabel('Mesa Nº')
                ->setColumns(4),

            ChoiceField::new('estado')
                ->setLabel('Estado')
                ->setChoices($this->getEstadoChoices())
                ->setRequired(true)
                ->renderExpanded(false)
                ->allowMultipleChoices(false)
                ->setColumns(4),

            TextareaField::new('infoAdicional')
                ->setLabel('Información Adicional')
                ->hideOnIndex()
                ->setNumOfRows(3)
                ->setMaxLength(500),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('nombreCliente')->setLabel('Cliente'))
            ->add(TextFilter::new('emailCliente')->setLabel('Email'))
            ->add(DateTimeFilter::new('fechaHoraReserva')->setLabel('Fecha/Hora'))
            ->add(NumericFilter::new('numeroComensales')->setLabel('Comensales'))
            ->add(NumericFilter::new('numeroMesa')->setLabel('Mesa'))
            ->add('estado');
    }

    /**
     * Obtiene las opciones del enum EstadoReserva para el campo choice
     */
    private function getEstadoChoices(): array
    {
        $choices = [];
        foreach (EstadoReserva::cases() as $estado) {
            // Asumiendo que tu enum tiene un método para obtener el label
            // Si no tienes un método, puedes usar directamente $estado->value
            $choices[$this->getEstadoLabel($estado)] = $estado;
        }
        return $choices;
    }

    /**
     * Obtiene la etiqueta legible para cada estado
     * Personaliza estos labels según tus necesidades
     */
    private function getEstadoLabel(EstadoReserva $estado): string
    {
        return match ($estado) {
            EstadoReserva::Pendiente => 'Pendiente',
            EstadoReserva::Confirmado => 'Confirmada',
            EstadoReserva::Cancelado => 'Cancelada',
            EstadoReserva::Completado => 'Completada',
            // Agrega más casos según los valores de tu enum
            default => $estado->value ?? $estado->name,
        };
    }

    /**
     * Personaliza la creación de nuevas reservas
     */
    public function createEntity(string $entityFqcn)
    {

        $reserva = new Reserva();
        // Establecer estado por defecto como PENDIENTE
        $reserva->setEstado(EstadoReserva::Pendiente);

        return $reserva;
    }

    /**
     * Método que se ejecuta después de persistir una nueva entidad
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);

        // Enviar email si es una nueva reserva
        if ($entityInstance instanceof Reserva) {
            $this->enviarEmailConfirmacion($entityInstance);
        }
        parent::persistEntity($entityManager, $entityInstance);

        $user = $this->security->getUser();
        $userId = $user ? $user->getUserIdentifier() : 'admin';

        $this->logger->info('Entidad creada', [
            'entidad' => get_class($entityInstance),
            'id' => method_exists($entityInstance, 'getId') ? $entityInstance->getId() : null,
            'usuario' => $userId,
        ]);
    }

    /**
     * Método que se ejecuta después de actualizar una entidad
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Reserva) {
            return;
        }
        $reservaAnterior = $entityManager->getUnitOfWork()->getOriginalEntityData($entityInstance);
        $estadoAnterior = $reservaAnterior['estado'] ?? null;
        $estadoNuevo = $entityInstance->getEstado();
        if ($estadoAnterior !== EstadoReserva::Confirmado && $estadoNuevo === EstadoReserva::Confirmado) {
            $this->enviarEmailConfirmacionAdmin($entityInstance);
        }
        parent::updateEntity($entityManager, $entityInstance);

        if ($estadoNuevo === EstadoReserva::Confirmado) {
            $usuario = $this->security->getUser();
            $this->logger->info('Reserva confirmada por el administrador', [
                'reserva_id' => $entityInstance->getId(),
                'cliente' => $entityInstance->getNombreCliente(),
                'fecha' => $entityInstance->getFechaHoraReserva()?->format('Y-m-d H:i'),
                'confirmada_por' => $usuario ? $usuario->getUserIdentifier() : 'admin',
            ]);
        }
    }

    /**
     * Envía email de confirmación al cliente cuando se crea la reserva
     */
    private function enviarEmailConfirmacion(Reserva $reserva): void
    {
        // Solo enviar si tiene email
        if (!$reserva->getEmailCliente()) {
            return;
        }

        try {
            $mailer = $this->mailer;

            $email = (new Email())
                ->from('alejal07@ucm.es')
                ->to($reserva->getEmailCliente())
                ->subject('Reserva recibida - Pendiente de confirmación')
                ->html($this->generarHtmlEmailPendiente($reserva));

            $mailer->send($email);
        } catch (\Exception $e) {
            $this->logger->error('Error enviando email: ' . $e->getMessage());
        }
    }

    /**
     * Envía email cuando el admin confirma la reserva
     */
    private function enviarEmailConfirmacionAdmin(Reserva $reserva): void
    {
        if (!$reserva->getEmailCliente()) {
            return;
        }

        try {
            $email = (new Email())
                ->from('alejal07@ucm.es')
                ->to($reserva->getEmailCliente())
                ->subject('¡Reserva confirmada!')
                ->html($this->generarHtmlEmailConfirmada($reserva));

            $this->mailer->send($email);
        } catch (\Exception $e) {
        }
    }


    /**
     * Genera el HTML para email de reserva pendiente
     */
    private function generarHtmlEmailPendiente(Reserva $reserva): string
    {
        return sprintf(
            '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                <h2 style="color: #333;">Hemos recibido tu reserva</h2>
                <p>Hola <strong>%s</strong>,</p>
                <p>Hemos recibido tu reserva y está <strong>pendiente de confirmación</strong>.</p>
                
                <div style="background-color: #f5f5f5; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <h3 style="margin-top: 0; color: #555;">Detalles de tu reserva:</h3>
                    <p><strong>Fecha y hora:</strong> %s</p>
                    <p><strong>Comensales:</strong> %s</p>
                    %s
                    %s
                </div>
                
                <p>Te confirmaremos la disponibilidad en breve.</p>
                <p>¡Gracias por elegir nuestro restaurante!</p>
            </div>
        ',
            $reserva->getNombreCliente(),
            $reserva->getFechaHoraReserva()?->format('d/m/Y H:i'),
            $reserva->getNumeroComensales(),
            $reserva->getNumeroMesa() ? '<p><strong>Mesa:</strong> ' . $reserva->getNumeroMesa() . '</p>' : '',
            $reserva->getInfoAdicional() ? '<p><strong>Información adicional:</strong> ' . $reserva->getInfoAdicional() . '</p>' : ''
        );
    }

    /**
     * Genera el HTML para email de reserva confirmada
     */
    private function generarHtmlEmailConfirmada(Reserva $reserva): string
    {
        return sprintf(
            '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                <h2 style="color: #28a745;">¡Tu reserva ha sido confirmada!</h2>
                <p>Hola <strong>%s</strong>,</p>
                <p>Nos complace confirmar tu reserva en el restaurante El Cañaveral</p>
                
                <div style="background-color: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #28a745;">
                    <h3 style="margin-top: 0; color: #155724;">Detalles confirmados:</h3>
                    <p><strong>Fecha y hora:</strong> %s</p>
                    <p><strong>Comensales:</strong> %s</p>
                    %s
                    %s
                </div>
                
                <p>Te esperamos en la fecha y hora indicada.</p>
                <p><strong>Dirección:</strong> Avenida de la ONU número 81, Móstoles</p>
                <p><strong>Teléfono:</strong> 642524636</p>
                
                <p>¡Nos vemos pronto!</p>
            </div>
        ',
            $reserva->getNombreCliente(),
            $reserva->getFechaHoraReserva()?->format('d/m/Y H:i'),
            $reserva->getNumeroComensales(),
            $reserva->getNumeroMesa() ? '<p><strong>Mesa asignada:</strong> ' . $reserva->getNumeroMesa() . '</p>' : '',
            $reserva->getInfoAdicional() ? '<p><strong>Información adicional:</strong> ' . $reserva->getInfoAdicional() . '</p>' : ''
        );
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::deleteEntity($entityManager, $entityInstance);

        $user = $this->security->getUser();
        $userId = $user ? $user->getUserIdentifier() : 'admin';

        $this->logger->info('Entidad eliminada', [
            'entidad' => get_class($entityInstance),
            'id' => method_exists($entityInstance, 'getId') ? $entityInstance->getId() : null,
            'usuario' => $userId,
        ]);
    }
}
