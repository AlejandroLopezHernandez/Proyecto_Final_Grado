<?php

namespace App\Controller\Admin;

use App\Entity\Bebida;
//use App\Entity\Comanda;
use App\Entity\Comida;
use App\Entity\Estilo;
use App\Entity\Fabricante;
use App\Entity\Producto;
use App\Entity\Proveedor;
use App\Entity\Reserva;
use App\Entity\Usuario;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AdminDashboard(routePath: '/admin', routeName: 'dashboardAdmin')]
class AdminDashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->render('admin/adminDashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Panel del Administrador');
    }
    public function createOrEditUser(Usuario $usuario, UserPasswordHasherInterface $PasswordHasher)
    {
        $plaintextPassword = $usuario->getPassword();
        $hashedPassword = $PasswordHasher->hashPassword($usuario, $plaintextPassword);
        $usuario->setPassword($hashedPassword);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
        yield MenuItem::linkToCrud('Productos', 'fas fa-list', Producto::class);
        yield MenuItem::linkToCrud('Usuario', 'fas fa-list', Usuario::class);
        yield MenuItem::linkToCrud('Bebidas', 'fas fa-list', Bebida::class);
        yield MenuItem::linkToCrud('Comida', 'fas fa-list', Comida::class);
        yield MenuItem::linkToCrud('Fabricantes', 'fas fa-list', Fabricante::class);
        yield MenuItem::linkToCrud('Estilos', 'fas fa-list', Estilo::class);
        yield MenuItem::linkToCrud('Proveedores', 'fas fa-list', Proveedor::class);
        yield MenuItem::linkToCrud('Reservas', 'fas fa-list', Reserva::class);
    }
}
