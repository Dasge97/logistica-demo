<?php

namespace App\Modulos\Catalogos\Infrastructure\Persistence\Doctrine;

use App\Modulos\Catalogos\Domain\Entity\TipoVehiculo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TipoVehiculo>
 */
final class RepositorioTipoVehiculo extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipoVehiculo::class);
    }
}
