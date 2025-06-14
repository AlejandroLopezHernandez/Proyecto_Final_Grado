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
    public function findCategoriasUnicas(): array
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c.categoria');

        $categorias = [];

        foreach ($qb->getQuery()->getResult() as $fila) {
            foreach ($fila['categoria'] as $cat) {
                $categorias[] = $cat;
            }
        }

        return array_unique($categorias);
    }

    public function findByCategoria(string $categoria): array
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.categoria LIKE :categoria')
            ->setParameter('categoria', '%"' . $categoria . '"%');

        return $qb->getQuery()->getResult();
    }

    public function findOpcionesByProductoId(int $id): array
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c.opciones')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        $result = $qb->getOneOrNullResult();

        $opciones = $result['opciones'] ?? [];

        // Aseguramos que sea array
        if (is_string($opciones)) {
            $opciones = json_decode($opciones, true);
        }

        return is_array($opciones) ? $opciones : [];
    }

    public function comidasXtipo(): array
    {
        $comidas = $this->findAll(); // recuperamos todas las comidas
        $conteoCategorias = [];

        foreach ($comidas as $comida) {
            $categorias = $comida->getCategoria(); // debe devolver array (campo tipo JSON)

            if (!is_array($categorias)) {
                continue;
            }

            foreach ($categorias as $categoria) {
                if (!isset($conteoCategorias[$categoria])) {
                    $conteoCategorias[$categoria] = 0;
                }
                $conteoCategorias[$categoria]++;
            }
        }

        // Ordenamos de mayor a menor
        arsort($conteoCategorias);

        // Convertimos en array listo para el JSON
        $resultado = [];
        foreach ($conteoCategorias as $categoria => $numero_comidas) {
            $resultado[] = [
                'categoria' => $categoria,
                'numero_comidas' => $numero_comidas
            ];
        }
        return $resultado;
    }

    public function ComidasXprecio(): array
    {
        $result = $this->createQueryBuilder('c')
            ->select([
                "CASE 
                WHEN c.pvp < 10 THEN 'Menos de 10€'
                WHEN c.pvp >= 10 AND c.pvp <= 15 THEN '10€ - 15€'
                WHEN c.pvp > 15 THEN 'Más de 15€'
                ELSE 'Sin precio'
            END AS rango_precio",
                'COUNT(c.id) AS cantidad_comidas'
            ])
            ->groupBy('rango_precio')
            ->orderBy(
                '
            CASE 
                WHEN rango_precio = \'Menos de 10€\' THEN 1
                WHEN rango_precio = \'10€ - 15€\' THEN 2
                WHEN rango_precio = \'Más de 15€\' THEN 3
                ELSE 4
            END'
            )
            ->getQuery()
            ->getResult();

        // Añadir los nombres concatenados manualmente
        foreach ($result as &$item) {
            $comidas = $this->createQueryBuilder('c')
                ->select('c.nombre')
                ->where("CASE 
                WHEN c.pvp < 10 THEN 'Menos de 10€'
                WHEN c.pvp >= 10 AND c.pvp <= 15 THEN '10€ - 15€'
                WHEN c.pvp > 15 THEN 'Más de 15€'
                ELSE 'Sin precio'
            END = :rango")
                ->setParameter('rango', $item['rango_precio'])
                ->getQuery()
                ->getScalarResult();

            $item['comidas_en_rango'] = implode(', ', array_column($comidas, 'nombre'));
        }

        return $result;
    }
}
