<?php

namespace App\Controller\Admin;

use App\Controller\ProductoComidaController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use App\Entity\Comida;
use App\Enum\CategoriaComida;
use App\Enum\VegetarianoVeganoSeleccion;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

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
            TextField::new('descripcion'),
            TextField::new('ingredientes'),
            ChoiceField::new('dieta')
                ->setChoices([
                    'Vegetariana' => VegetarianoVeganoSeleccion::Vegetariano,
                    'Vegana' => VegetarianoVeganoSeleccion::Vegano,
                ])
                ->renderAsNativeWidget(),
            NumberField::new('stock'),
            ChoiceField::new('categoria')
                ->setChoices([
                    'Aperitivos' => CategoriaComida::Aperitivos,
                    'Hamburguesas' => CategoriaComida::Hamburguesas,
                    'Pizzas' => CategoriaComida::Pizzas,
                    'Sandwiches' => CategoriaComida::Sandwiches,
                    'Postres' => CategoriaComida::Postres,
                ])
                ->renderAsNativeWidget(),
            CollectionField::new('productoComidas', 'productos')
                ->useEntryCrudForm(ProductoComidaCrudController::class)
                ->setFormTypeOptions(['by_reference' => false])
                ->setRequired(false)
        ];
    }
}
