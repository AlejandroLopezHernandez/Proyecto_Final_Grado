<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use App\Entity\Usuario;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UsuarioCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Usuario::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nombre'),
            TextField::new('foto'),
            BooleanField::new('estado'),
            TextField::new('password'),
            ChoiceField::new('roles')
                ->setChoices([
                    'Administrador' => 'ROLE_ADMIN',
                    'Manager' => 'ROLE_MANAGER',
                    'Camarero' => 'ROLE_CAMARERO',
                ])
                ->allowMultipleChoices()
                ->renderExpanded()
        ];
    }
}
