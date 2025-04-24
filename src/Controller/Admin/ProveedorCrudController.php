<?php

namespace App\Controller\Admin;

use App\Entity\Proveedor;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

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
        ];
    }
}
