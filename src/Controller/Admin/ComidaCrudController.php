<?php

namespace App\Controller\Admin;

use App\Entity\Comida;
use App\Entity\Producto;
use App\Enum\CategoriaComida;
use App\Enum\VegetarianoVeganoSeleccion;
use App\Enum\OpcionesQuitar;
use App\Enum\OpcionesAnadir;
use App\Enum\PuntoCoccion;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

class ComidaCrudController extends AbstractCrudController
{
    private AdminUrlGenerator $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Comida::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::INDEX);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nombre'),

            AssociationField::new('productos')
                ->setLabel('Productos utilizados')
                ->setFormTypeOption('by_reference', false)
                ->hideOnIndex()
                ->formatValue(function ($value, $entity) {
                    return implode('<br>', $entity->getProductos()->map(function ($producto) {
                        $url = $this->adminUrlGenerator
                            ->setController(ProductoCrudController::class)
                            ->setAction(Action::DETAIL)
                            ->setEntityId($producto->getId())
                            ->generateUrl();

                        return sprintf('<a href="%s">%s</a>', $url, htmlspecialchars($producto->getNombre()));
                    })->toArray());
                })
                ->renderAsHtml(),

            ChoiceField::new('categoria')
                ->setLabel('Categorías')
                ->setChoices(CategoriaComida::eleccionMultipleParaCrud())
                ->renderAsNativeWidget(false)
                ->allowMultipleChoices()
                ->setRequired(false),

            ChoiceField::new('dieta')
                ->setLabel('Tipo de dieta')
                ->setChoices(VegetarianoVeganoSeleccion::eleccionParaCrud())
                ->renderAsNativeWidget(),

            ChoiceField::new('opciones')
                ->setLabel('Opciones disponibles')
                ->allowMultipleChoices()
                ->setChoices(array_merge(
                    OpcionesQuitar::todas(),
                    OpcionesAnadir::todas(),
                    PuntoCoccion::todas()
                )),

            NumberField::new('stock'),
            NumberField::new('pvp'),
            TextareaField::new('descripcion'),
            TextareaField::new('receta')
                ->setLabel('Receta completa')
                ->setNumOfRows(10)
        ];
    }
}
