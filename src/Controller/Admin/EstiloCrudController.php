<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use App\Entity\Estilo;
use App\Enum\TipoBebida;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EstiloCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Estilo::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nombre'),
            TextField::new('descripcion'),
            TextField::new('maridaje'),
            ChoiceField::new('tipo_bebida')
                ->setChoices([
                    'Cerveza' => TipoBebida::Cerveza,
                    'Vinos' => TipoBebida::Vinos,
                    'Licores' => TipoBebida::Licores,
                    'NoAlcoholica' => TipoBebida::NoAlcoholica,

                ])
                ->renderAsNativeWidget()
        ];
    }
}
