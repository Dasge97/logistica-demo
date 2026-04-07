<?php

namespace App\Modulos\Pedidos\Infrastructure\Persistence\Doctrine;

use App\Modulos\Pedidos\Domain\Entity\Pedido;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pedido>
 */
final class RepositorioPedido extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pedido::class);
    }
}
