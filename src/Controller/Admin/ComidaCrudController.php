<?php

namespace App\Controller\Admin;

use App\Entity\Comida;
use App\Enum\CategoriaComida;
use App\Enum\VegetarianoVeganoSeleccion;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use App\Enum\OpcionesQuitar;
use App\Enum\OpcionesAnadir;
use App\Enum\PuntoCoccion;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;


class ComidaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comida::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nombre'),
            
            AssociationField::new('productos')
                ->setLabel('Productos utilizados')
                ->setFormTypeOption('by_reference', false)
                ->autocomplete()
                ->setCrudController(ProductoCrudController::class),

            ChoiceField::new('categoria')
                ->setLabel('Categorías')
                ->setChoices(CategoriaComida::eleccionMultipleParaCrud())
                ->renderAsNativeWidget(false)
                ->allowMultipleChoices()
                ->setRequired(false),

            ChoiceField::new('dieta')
                ->setLabel('Tipo de dieta')
                ->setChoices(VegetarianoVeganoSeleccion::eleccionParaCrud())
                ->renderAsNativeWidget(),

            // Configuración de opciones
            ChoiceField::new('opciones')
                ->setLabel('Opciones disponibles')
                ->allowMultipleChoices()
                ->setChoices(array_merge(
                    OpcionesQuitar::todas(),
                    OpcionesAnadir::todas(),
                    PuntoCoccion::todas()
                )),

            NumberField::new('stock'),
            NumberField::new('precio'),
            TextareaField::new('descripcion'),
            TextareaField::new('receta')
                ->setLabel('Receta completa')
                ->setNumOfRows(10)
        ];
    }
}
