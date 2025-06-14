<?php

namespace App\Controller\Admin;

use App\Entity\Fabricante;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class FabricanteCrudController extends AbstractCrudController
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
        return Fabricante::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // Habilitar para todas las páginas (index, detail, edit, etc.)
            ->add(Crud::PAGE_INDEX, Action::DETAIL) // 👈 Añade el botón de detalle
            ->add(Crud::PAGE_EDIT, Action::INDEX)   // Opcional: añade botón para volver al listado

        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nombre'),
            TextField::new('pais'),
            TextareaField::new('descripcion'),
            TextField::new('imagen'),
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
