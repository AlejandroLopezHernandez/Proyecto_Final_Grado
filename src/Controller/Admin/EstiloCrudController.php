<?php

namespace App\Controller\Admin;

use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use App\Entity\Estilo;
use App\Enum\TipoFermentacion;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use App\Controller\Admin\BebidaCrudController;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;


class EstiloCrudController extends AbstractCrudController
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
        return Estilo::class;
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
            TextField::new('origen'),
            ChoiceField::new('fermentacion')
                ->setLabel('Fermentación')
                ->setChoices(TipoFermentacion::eleccionParaCrud())
                ->renderAsNativeWidget(),
            TextField::new('color'),
            TextField::new('sabor'),
            TextField::new('aroma'),
            TextField::new('carbonatacion'),
            TextareaField::new('descripcion'),
            TextField::new('maridaje'),

            AssociationField::new('estiloPadre')
                ->setLabel('Estilo Padre')
                ->setCrudController(EstiloCrudController::class)
                ->autocomplete()
                ->setQueryBuilder(function (QueryBuilder $qb) {
                    $currentId = $this->getContext()->getEntity()->getInstance()?->getId();
                    $qb = $qb->getEntityManager()->createQueryBuilder()
                        ->select('e')
                        ->from(Estilo::class, 'e');

                    if ($currentId) {
                        $qb->where('e.id != :currentId')
                            ->setParameter('currentId', $currentId);
                    }

                    return $qb;
                })
                ->setRequired(false),

            AssociationField::new('subestilos')
                ->setLabel('Subestilos Asociados')
                ->formatValue(function ($value, $entity) {
                    return implode('<br>', $entity->getSubestilos()->map(function ($subestilo) {
                        $url = $this->adminUrlGenerator
                            ->setController(EstiloCrudController::class)
                            ->setAction(Action::DETAIL)
                            ->setEntityId($subestilo->getId())
                            ->generateUrl();

                        return sprintf('<a href="%s">%s</a>', $url, htmlspecialchars($subestilo->getNombre()));
                    })->toArray());
                })
                ->renderAsHtml()
                ->hideOnForm(),

            AssociationField::new('bebidas')
                ->setLabel('Bebidas asociadas')
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
                ->renderAsHtml()
                ->hideOnForm(),
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
