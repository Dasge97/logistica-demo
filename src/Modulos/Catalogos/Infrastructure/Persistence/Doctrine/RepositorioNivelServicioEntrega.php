<?php

namespace App\Modulos\Catalogos\Infrastructure\Persistence\Doctrine;

use App\Modulos\Catalogos\Domain\Entity\NivelServicioEntrega;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NivelServicioEntrega>
 */
final class RepositorioNivelServicioEntrega extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NivelServicioEntrega::class);
    }
}
