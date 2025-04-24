<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use App\Entity\Comida;
use App\Enum\CategoriaComida;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
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
            NumberField::new('stock'),
            ChoiceField::new('categoria')
                ->setChoices([
                    'Aperitivos' => CategoriaComida::Aperitivos,
                    'Hamburguesas' => CategoriaComida::Hamburguesas,
                    'Pizzas' => CategoriaComida::Pizzas,
                    'Sandwiches' => CategoriaComida::Sandwiches,
                    'Postres' => CategoriaComida::Postres,
                ])
                ->renderAsNativeWidget()
        ];
    }
}
