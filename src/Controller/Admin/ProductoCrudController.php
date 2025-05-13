<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use App\Entity\Producto;
use App\Enum\CategoriaProducto;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductoCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Producto::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nombre'),
            NumberField::new('coste'),
            NumberField::new('stock'),
            TextField::new('medida'),
            TextField::new('descripcion'),
            ChoiceField::new('Categoria')
                ->setChoices(CategoriaProducto::eleccionParaCrud())
                ->renderAsNativeWidget(),
            AssociationField::new('proveedores') //  Debe coincidir con la propiedad de la entidad
                ->setLabel('Proveedores')
                ->setFormTypeOption('by_reference', false)
                ->formatValue(function ($value, $entity) {
                    return implode(', ', $entity->getProveedores()->map(fn($p) => $p->getNombre())->toArray());
                })
                ->setFormTypeOption('choice_label', 'nombre'),
            AssociationField::new('comidas')
                ->setLabel('Comidas que lo usan')
                ->onlyOnIndex() // Solo visible en el listado y no en el formulario de crear
                ->formatValue(function ($value, $entity) {
                    return implode(', ', $entity->getComidas()->map(fn($c) => $c->getNombre())->toArray());
                })
                ->setFormTypeOption('choice_label', 'nombre')
        ];
    }
}
