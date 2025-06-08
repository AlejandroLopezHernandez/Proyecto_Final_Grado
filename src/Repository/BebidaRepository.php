<?php

namespace App\Repository;

use App\Entity\Bebida;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bebida>
 */
class BebidaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bebida::class);
    }
    public function cervezasXestilo(): array
    {
        return $this->createQueryBuilder('b')
            ->select('e.nombre AS estilo_cerveza', 'COUNT(b.id) AS numero_cervezas')
            ->join('b.estilo', 'e')
            ->where("b.tipoBebida = 'cerveza'")
            ->groupBy('e.id')
            ->getQuery()
            ->getResult();
    }
    public function contarBebidasPorTipo(): array
    {
        return $this->createQueryBuilder('b')
            ->select('b.tipoBebida AS tipo_bebida', 'COUNT(b.id) AS numero_bebidas')
            ->groupBy('b.tipoBebida')
            ->getQuery()
            ->getResult();
    }
    public function BebidasPorRangoPrecio(): array
    {
        // Primera consulta para rangos y conteos
        $result = $this->createQueryBuilder('b')
            ->select([
                "CASE 
                WHEN b.pvp < 3 THEN 'Menos de 3€'
                WHEN b.pvp >= 3 AND b.pvp <= 6 THEN '3€ - 6€'
                WHEN b.pvp > 6 THEN 'Más de 6€'
                ELSE 'Sin precio'
            END AS rango_precio",
                'COUNT(b.id) AS cantidad_bebidas'
            ])
            ->groupBy('rango_precio')
            ->orderBy(
                '
            CASE 
                WHEN rango_precio = \'Menos de 3€\' THEN 1
                WHEN rango_precio = \'3€ - 6€\' THEN 2
                WHEN rango_precio = \'Más de 6€\' THEN 3
                ELSE 4
            END'
            )
            ->getQuery()
            ->getResult();

        // Segunda consulta para obtener nombres concatenados
        foreach ($result as &$item) {
            $bebidas = $this->createQueryBuilder('b')
                ->select('b.nombre')
                ->where("CASE 
                WHEN b.pvp < 3 THEN 'Menos de 3€'
                WHEN b.pvp >= 3 AND b.pvp <= 6 THEN '3€ - 6€'
                WHEN b.pvp > 6 THEN 'Más de 6€'
                ELSE 'Sin precio'
            END = :rango")
                ->setParameter('rango', $item['rango_precio'])
                ->getQuery()
                ->getScalarResult();

            $item['bebidas_en_rango'] = implode(', ', array_column($bebidas, 'nombre'));
        }

        return $result;
    }
}
