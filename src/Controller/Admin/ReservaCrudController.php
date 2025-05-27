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
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

class ReservaCrudController extends AbstractCrudController
{
    private MailerInterface $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
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
    }

    /**
     * Método que se ejecuta después de actualizar una entidad
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Reserva) {
            return;
        }

        // Obtener el estado anterior
        $reservaAnterior = $entityManager->getUnitOfWork()->getOriginalEntityData($entityInstance);
        $estadoAnterior = $reservaAnterior['estado'] ?? null;
        $estadoNuevo = $entityInstance->getEstado();

        parent::updateEntity($entityManager, $entityInstance);

        // Solo enviar email si ha cambiado a CONFIRMADO
        if ($estadoAnterior !== EstadoReserva::Confirmado && $estadoNuevo === EstadoReserva::Confirmado) {
            $this->enviarEmailConfirmacionAdmin($entityInstance);
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
                ->from('alejal07@ucm.es') // Cambia por tu email
                ->to($reserva->getEmailCliente())
                ->subject('Reserva recibida - Pendiente de confirmación')
                ->html($this->generarHtmlEmailPendiente($reserva));

            $mailer->send($email);
        } catch (\Exception $e) {
            // Log del error pero no interrumpir el proceso
            // $this->logger->error('Error enviando email: ' . $e->getMessage());
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
                <p>Nos complace confirmar tu reserva en nuestro restaurante.</p>
                
                <div style="background-color: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #28a745;">
                    <h3 style="margin-top: 0; color: #155724;">Detalles confirmados:</h3>
                    <p><strong>Fecha y hora:</strong> %s</p>
                    <p><strong>Comensales:</strong> %s</p>
                    %s
                    %s
                </div>
                
                <p>Te esperamos en la fecha y hora indicada.</p>
                <p><strong>Dirección:</strong> [Tu dirección aquí]</p>
                <p><strong>Teléfono:</strong> [Tu teléfono aquí]</p>
                
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
}
    // Opcional: Personalizar acciones
    /*
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Nueva Reserva')->setIcon('fa fa-plus');
            })
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setLabel('Editar')->setIcon('fa fa-edit');
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setLabel('Eliminar')->setIcon('fa fa-trash');
            });
    }
    */
