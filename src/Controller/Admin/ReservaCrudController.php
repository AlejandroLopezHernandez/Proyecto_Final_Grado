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

class ReservaCrudController extends AbstractCrudController
{
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
        // Establecer valores por defecto
        $reserva->setEstado(EstadoReserva::Pendiente);

        return $reserva;
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
}
