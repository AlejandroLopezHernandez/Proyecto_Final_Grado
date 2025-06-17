<?php

namespace App\Repository;

use App\Entity\Mesa;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Mesa>
 */
class MesaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mesa::class);
    }
    /**
     * Elimina todas las filas de la tabla mesa.
     * Advertencia: hace DELETE FROM App\Entity\Mesa
     */
    public function deleteAllMesas(): int
    {
        // Usamos QueryBuilder para DELETE
        $qb = $this->createQueryBuilder('m');
        $q = $qb
            ->delete()
            ->getQuery();
        return $q->execute();
    }

    /**
     * Devuelve la última Mesa creada, ordenando por ID descendente.
     * @return Mesa|null
     */
    public function findUltimaMesa(): ?Mesa
    {
        return $this->findOneBy([], ['id' => 'DESC']);
    }
}
