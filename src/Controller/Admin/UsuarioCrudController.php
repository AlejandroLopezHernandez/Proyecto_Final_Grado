<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use App\Entity\Usuario;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;


class UsuarioCrudController extends AbstractCrudController
{
    //Método para guardar contraseñas con hash en la base de datos
    private UserPasswordHasherInterface $passwordHasher;
    private AdminUrlGenerator $adminUrlGenerator;
    private $logger;
    private $security;
    public function __construct(UserPasswordHasherInterface $passwordHasher, AdminUrlGenerator $adminUrlGenerator, LoggerInterface $logger, Security $security)
    {
        $this->passwordHasher = $passwordHasher;
        $this->passwordHasher = $passwordHasher;
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->logger = $logger;
        $this->security = $security;
    }

    public static function getEntityFqcn(): string
    {
        return Usuario::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // Habilitar para todas las páginas (index, detail, edit, etc.)
            ->add(Crud::PAGE_INDEX, Action::DETAIL) // 👈 Añade el botón de detalle
            ->add(Crud::PAGE_EDIT, Action::INDEX)   // Opcional: añade botón para volver al listado

        ;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Usuario) {
            $plainPassword = $entityInstance->getPassword();
            $hashedPassword = $this->passwordHasher->hashPassword($entityInstance, $plainPassword);
            $entityInstance->setPassword($hashedPassword);
        }

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
        if ($entityInstance instanceof Usuario) {
            $plainPassword = $entityInstance->getPassword();
            // Solo hashea si no está ya hasheada
            if (!str_starts_with($plainPassword, '$2y$')) {
                $hashedPassword = $this->passwordHasher->hashPassword($entityInstance, $plainPassword);
                $entityInstance->setPassword($hashedPassword);
            }
        }
        parent::updateEntity($entityManager, $entityInstance);
        $user = $this->security->getUser();
        $userId = $user ? $user->getUserIdentifier() : 'admin';

        $this->logger->info('Entidad actualizada', [
            'entidad' => get_class($entityInstance),
            'id' => method_exists($entityInstance, 'getId') ? $entityInstance->getId() : null,
            'usuario' => $userId,
        ]);
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
