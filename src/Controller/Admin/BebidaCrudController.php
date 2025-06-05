<?php

namespace App\Controller\Admin;

use Doctrine\ORM\QueryBuilder;
use App\Enum\TipoBebida;
use App\Entity\Bebida;
use App\Entity\Fabricante;
use App\Enum\FormatoBebida;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class BebidaCrudController extends AbstractCrudController
{
    private $logger;
    private $security;
    private AdminUrlGenerator $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator, LoggerInterface $logger, Security $security)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->logger = $logger;
        $this->security = $security;
    }
    public static function getEntityFqcn(): string
    {
        return Bebida::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // Habilitar para todas las páginas (index, detail, edit, etc.)
            ->add(Crud::PAGE_INDEX, Action::DETAIL) // Añade el botón de detalle
            ->add(Crud::PAGE_EDIT, Action::INDEX)   // Opcional: añade botón para volver al listado

        ;
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nombre'),
            ChoiceField::new('tipoBebida')
                ->setLabel('Categoría')
                ->setChoices(TipoBebida::eleccionParaCrud())
                ->renderAsNativeWidget(),
            AssociationField::new('estilo')
                ->setLabel('Estilo o Subcategoría')
                ->setCrudController(EstiloCrudController::class)
                ->autocomplete(),
            AssociationField::new('fabricante')
                ->setLabel('Fabricante')
                ->autocomplete()
                ->setQueryBuilder(function (QueryBuilder $qb) {
                    return $qb->getEntityManager()
                        ->createQueryBuilder()
                        ->select('f')
                        ->from(Fabricante::class, 'f')
                        ->orderBy('f.nombre', 'ASC');
                }),
            TextareaField::new('descripcion'),
            NumberField::new('grado_alcoholico'),
            TextField::new('lupulos'),
            NumberField::new('coste'),
            NumberField::new('pvp'),
            NumberField::new('stock'),
            ChoiceField::new('formato')
                ->setChoices(FormatoBebida::eleccionParaCrud()) // Hecho en el Enum
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
