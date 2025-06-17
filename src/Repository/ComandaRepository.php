<?php

namespace App\Repository;

use App\Entity\Comanda;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comanda>
 */
class ComandaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comanda::class);
    }
    /**
     * Obtiene las cervezas más vendidas
     */
    public function getCervezasMasVendidas(int $limit = 5): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
    SELECT 
        JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\')) as nombre,
        SUM(JSON_EXTRACT(item.value, \'$.cantidad\')) as total_vendido
    FROM comanda c,
         JSON_TABLE(c.productos, \'$[*]\' COLUMNS(
             value JSON PATH \'$\'
         )) as item
    WHERE LOWER(JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\'))) LIKE \'%cerveza%\'
    GROUP BY nombre
    ORDER BY total_vendido DESC
    LIMIT ' . (int)$limit . '
    ';

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }
    public function getBebidasMasVendidas(int $limit = 5): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
    SELECT 
        JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\')) as nombre,
        SUM(JSON_EXTRACT(item.value, \'$.cantidad\')) as total_vendido,
        SUM(JSON_EXTRACT(item.value, \'$.precio\') * JSON_EXTRACT(item.value, \'$.cantidad\')) as total_ventas
    FROM comanda c,
         JSON_TABLE(c.productos, \'$[*]\' COLUMNS(
             value JSON PATH \'$\'
         )) as item
    WHERE LOWER(JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\'))) LIKE \'%cerveza%\'
       OR LOWER(JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\'))) LIKE \'%refresco%\'
       OR LOWER(JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\'))) LIKE \'%agua%\'
       OR LOWER(JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\'))) LIKE \'%coca cola%\'
       OR LOWER(JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\'))) LIKE \'%fanta%\'
       OR LOWER(JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\'))) LIKE \'%aquarius%\'
       OR LOWER(JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\'))) LIKE \'%zumo%\'
       OR LOWER(JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\'))) LIKE \'%cafe%\'
       OR LOWER(JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\'))) LIKE \'%bebida%\'
    GROUP BY nombre
    ORDER BY total_vendido DESC
    LIMIT ' . (int)$limit . '
    ';

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }
    public function getComidasMasVendidas(int $limit = 5): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
    SELECT 
        JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\')) as nombre,
        SUM(JSON_EXTRACT(item.value, \'$.cantidad\')) as total_vendido,
        SUM(JSON_EXTRACT(item.value, \'$.precio\') * JSON_EXTRACT(item.value, \'$.cantidad\')) as total_ventas
    FROM comanda c,
         JSON_TABLE(c.productos, \'$[*]\' COLUMNS(
             value JSON PATH \'$\'
         )) as item
    WHERE LOWER(JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\'))) NOT LIKE \'%cerveza%\'
      AND LOWER(JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\'))) NOT LIKE \'%refresco%\'
      AND LOWER(JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\'))) NOT LIKE \'%agua%\'
      AND LOWER(JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\'))) NOT LIKE \'%coca cola%\'
      AND LOWER(JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\'))) NOT LIKE \'%fanta%\'
      AND LOWER(JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\'))) NOT LIKE \'%aquarius%\'
      AND LOWER(JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\'))) NOT LIKE \'%zumo%\'
      AND LOWER(JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\'))) NOT LIKE \'%cafe%\'
      AND LOWER(JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\'))) NOT LIKE \'%bebida%\'
    GROUP BY nombre
    ORDER BY total_vendido DESC
    LIMIT ' . (int)$limit . '
    ';

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }
    public function getRefrescosMasVendidos(int $limit = 5): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
    SELECT 
        JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\')) as nombre,
        SUM(JSON_EXTRACT(item.value, \'$.cantidad\')) as total_vendido,
        SUM(JSON_EXTRACT(item.value, \'$.precio\') * JSON_EXTRACT(item.value, \'$.cantidad\')) as total_ventas
    FROM comanda c,
         JSON_TABLE(c.productos, \'$[*]\' COLUMNS(
             value JSON PATH \'$\'
         )) as item
    WHERE LOWER(JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\'))) LIKE \'%refresco%\'
       OR LOWER(JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\'))) LIKE \'%cola%\'
       OR LOWER(JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\'))) LIKE \'%fanta%\'
       OR LOWER(JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\'))) LIKE \'%aquarius%\'
       OR LOWER(JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\'))) LIKE \'%nestea%\'
       OR LOWER(JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\'))) LIKE \'%sprite%\'
       OR LOWER(JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\'))) LIKE \'%7up%\'
       OR LOWER(JSON_UNQUOTE(JSON_EXTRACT(item.value, \'$.nombre\'))) LIKE \'%kas%\'
    GROUP BY nombre
    ORDER BY total_vendido DESC
    LIMIT ' . (int)$limit . '
    ';

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }
}
