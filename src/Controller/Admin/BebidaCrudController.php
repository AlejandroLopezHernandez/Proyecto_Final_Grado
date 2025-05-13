<?php

namespace App\Controller\Admin;

use App\Entity\Bebida;
use App\Enum\FormatoBebida;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class BebidaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Bebida::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nombre'),
            NumberField::new('grado_alcoholico'),
            NumberField::new('precio'),
            NumberField::new('stock'),
            ChoiceField::new('formato')
                ->setChoices(FormatoBebida::eleccionParaCrud()) // ¡Nuevo método!
                ->renderAsNativeWidget(),
            AssociationField::new('proveedores')
                ->setLabel('Proveedores')                
                ->setFormTypeOption('by_reference', false)
                ->formatValue(function ($value, $entity) {
                    return implode(', ', $entity->getProveedores()->map(fn($p) => $p->getNombre())->toArray());
                })
                ->setFormTypeOption('choice_label', 'nombre')
        ];
    }
}