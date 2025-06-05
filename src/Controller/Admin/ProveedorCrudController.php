<?php

namespace App\Controller\Admin;

use App\Entity\Proveedor;
use App\Controller\Admin\ProductoCrudController;
use App\Controller\Admin\BebidaCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\EntityManagerInterface;

class ProveedorCrudController extends AbstractCrudController
{
    private AdminUrlGenerator $adminUrlGenerator;
    private $logger;
    private $security;
    public function __construct(AdminUrlGenerator $adminUrlGenerator, LoggerInterface $logger, Security $security)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->logger = $logger;
        $this->security = $security;
    }

    public static function getEntityFqcn(): string
    {
        return Proveedor::class;
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
            TextField::new('email'),
            TextField::new('telefono'),
            TextField::new('descripcion'),

            AssociationField::new('bebidas')
                ->setLabel('Bebidas asociadas')
                ->hideOnForm()
                ->formatValue(function ($value, $entity) {
                    return implode('<br>', $entity->getBebidas()->map(function ($bebida) {
                        $url = $this->adminUrlGenerator
                            ->setController(BebidaCrudController::class)
                            ->setAction(Action::DETAIL)
                            ->setEntityId($bebida->getId())
                            ->generateUrl();

                        return sprintf('<a href="%s">%s</a>', $url, htmlspecialchars($bebida->getNombre()));
                    })->toArray());
                })
                ->renderAsHtml(),

            AssociationField::new('productos')
                ->setLabel('Productos asociados')
                ->hideOnForm()
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
                ->renderAsHtml()
        ];
    }
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);

        $user = $this->security->getUser();
        $userId = $user ? $user->getUserIdentifier() : 'admin';

        $this->logger->info('Entidad creada', [
            'entidad' => get_class($entityInstance),
            'id' => method_exists($entityInstance, 'getId') ? $entityInstance->getId() : null,
            'usuario' => $userId,
        ]);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::updateEntity($entityManager, $entityInstance);

        $user = $this->security->getUser();
        $userId = $user ? $user->getUserIdentifier() : 'admin';

        $this->logger->info('Entidad actualizada', [
            'entidad' => get_class($entityInstance),
            'id' => method_exists($entityInstance, 'getId') ? $entityInstance->getId() : null,
            'usuario' => $userId,
        ]);
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::deleteEntity($entityManager, $entityInstance);

        $user = $this->security->getUser();
        $userId = $user ? $user->getUserIdentifier() : 'admin';

        $this->logger->info('Entidad eliminada', [
            'entidad' => get_class($entityInstance),
            'id' => method_exists($entityInstance, 'getId') ? $entityInstance->getId() : null,
            'usuario' => $userId,
        ]);
    }
}
