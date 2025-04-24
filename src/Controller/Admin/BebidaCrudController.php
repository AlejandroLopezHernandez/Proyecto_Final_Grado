<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use App\Entity\Bebida;
use App\Enum\FormatoBebida;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

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
                ->setChoices([
                    'Pinta' => FormatoBebida::Pinta,
                    'MediaPinta' => FormatoBebida::MediaPinta,
                    'Canya' => FormatoBebida::Canya,
                    'CafeLeche' => FormatoBebida::CafeLeche,
                    'CafeExpresso' => FormatoBebida::CafeExpresso
                ])
                ->renderAsNativeWidget()
        ];
    }
}
