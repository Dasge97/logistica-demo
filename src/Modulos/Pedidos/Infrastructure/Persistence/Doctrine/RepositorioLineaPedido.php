<?php

namespace App\Modulos\Pedidos\Infrastructure\Persistence\Doctrine;

use App\Modulos\Pedidos\Domain\Entity\LineaPedido;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LineaPedido>
 */
final class RepositorioLineaPedido extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LineaPedido::class);
    }
}
