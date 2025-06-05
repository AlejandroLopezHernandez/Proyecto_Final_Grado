<?php

namespace App\Controller\Admin;

use App\Entity\Producto;
use App\Controller\Admin\ProveedorCrudController;
use App\Controller\Admin\ComidaCrudController;
use App\Enum\CategoriaProducto;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ProductoCrudController extends AbstractCrudController
{
    private AdminUrlGenerator $adminUrlGenerator;

    public function __construct(
        #[Autowire(service: AdminUrlGenerator::class)] AdminUrlGenerator $adminUrlGenerator
    ) {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Producto::class;
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
            NumberField::new('coste'),
            NumberField::new('stock'),
            TextField::new('medida'),
            TextField::new('descripcion'),
            ChoiceField::new('Categoria')
                ->setChoices(CategoriaProducto::eleccionParaCrud())
                ->renderAsNativeWidget(),

            AssociationField::new('proveedores')
                ->setLabel('Proveedores')
                ->setFormTypeOption('by_reference', false)
                ->formatValue(function ($value, $entity) {
                    $links = [];
                    foreach ($entity->getProveedores() as $p) {
                        $url = $this->adminUrlGenerator
                            ->setController(ProveedorCrudController::class)
                            ->setAction('detail')
                            ->setEntityId($p->getId())
                            ->generateUrl();
                        $links[] = sprintf('<a href="%s">%s</a>', $url, $p->getNombre());
                    }
                    return implode(', ', $links);
                })
                ->renderAsHtml(),

            AssociationField::new('comidas')
                ->setLabel('Comidas que lo usan')
                ->hideOnForm()
                ->formatValue(function ($value, $entity) {
                    $links = [];
                    foreach ($entity->getComidas() as $c) {
                        $url = $this->adminUrlGenerator
                            ->setController(ComidaCrudController::class)
                            ->setAction('detail')
                            ->setEntityId($c->getId())
                            ->generateUrl();
                        $links[] = sprintf('<a href="%s">%s</a>', $url, $c->getNombre());
                    }
                    return implode(', ', $links);
                })
                ->renderAsHtml()
        ];
    }
}
