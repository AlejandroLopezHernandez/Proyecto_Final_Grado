<?php

namespace App\Controller\Admin;

use App\Entity\ProductoComida;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductoComidaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProductoComida::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('Producto', 'producto')
                ->autocomplete(),
        ];
    }
}
