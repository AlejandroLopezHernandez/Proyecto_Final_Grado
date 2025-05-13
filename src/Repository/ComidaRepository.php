<?php

namespace App\Repository;

use App\Entity\Comida;
use App\Enum\CategoriaComida;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comida>
 */
class ComidaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comida::class);
    }

    //    /**
    //     * @return Comida[] Returns an array of Comida objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Comida
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

     /**
     * Devuelve un array con todas las categorías 
     */
    public function findCategoriasDisponibles(): array
    {
        $qb = $this->createQueryBuilder('c')
            ->select('DISTINCT c.Categoria')
            ->where('c.Categoria IS NOT NULL');

        return array_map(
            fn($row) => $row['Categoria'],
            $qb->getQuery()->getArrayResult()
        );
    }

    /**
     * Devuelve todas las comidas que pertenecen a una categoría .
     */
    public function findByCategoria(CategoriaComida $categoria): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.Categoria = :categoria')
            ->setParameter('categoria', $categoria)
            ->orderBy('c.Nombre', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

