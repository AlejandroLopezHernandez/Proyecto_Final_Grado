<?php

namespace App\Repository;

use App\Entity\Producto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Producto>
 */
class ProductoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Producto::class);
    }
    public function contarProductosPorProveedor(): array
    {
        return $this->createQueryBuilder('prod')
            ->select('prov.nombre AS nombre_proveedor', 'COUNT(prod.id) AS numero_productos')
            ->join('prod.proveedores', 'prov')
            ->groupBy('prov.id')
            ->getQuery()
            ->getResult();
    }
}
