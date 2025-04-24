<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use App\Entity\Producto;
use App\Enum\CategoriaProducto;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
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
            TextField::new('descripcion'),
            ChoiceField::new('categoria')
                ->setChoices([
                    'Comida' => CategoriaProducto::Comida,
                    'Bebida' => CategoriaProducto::Bebida,
                    'Otros' => CategoriaProducto::Otros,
                ])
                ->renderAsNativeWidget()
        ];
    }
}
