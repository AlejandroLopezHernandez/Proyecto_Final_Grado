<?php

namespace App\Controller\Admin;

use App\Entity\Proveedor;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class ProveedorCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Proveedor::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nombre'),
            TextField::new('email'),
            TextField::new('telefono'),
            TextField::new('descripcion'),
            AssociationField::new('bebidas')
                ->setLabel('Bebidas asociadas')
                ->onlyOnIndex() // Solo visible en el listado y no en el formulario de crear
                ->formatValue(function ($value, $entity) {
                    return implode(', ', $entity->getBebidas()->map(fn($b) => $b->getNombre())->toArray());
                })
                ->setFormTypeOption('choice_label', 'nombre'),
            AssociationField::new('productos')
                ->setLabel('Productos asociados')
                ->onlyOnIndex() // Solo visible en el listado y no en el formulario de crear
                ->formatValue(function ($value, $entity) {
                    return implode(', ', $entity->getProductos()->map(fn($p) => $p->getNombre())->toArray());
                })
                ->setFormTypeOption('choice_label', 'nombre')
        ];
    }
}
