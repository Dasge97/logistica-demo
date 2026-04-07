<?php

namespace App\Modulos\Decisiones\Infrastructure\Persistence\Doctrine;

use App\Modulos\Decisiones\Domain\Entity\SnapshotTarificacionPedido;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SnapshotTarificacionPedido>
 */
final class RepositorioSnapshotTarificacionPedido extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SnapshotTarificacionPedido::class);
    }
}
