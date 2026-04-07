<?php

namespace App\Modulos\Catalogos\Infrastructure\Persistence\Doctrine;

use App\Modulos\Catalogos\Domain\Entity\TipoCliente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TipoCliente>
 */
final class RepositorioTipoCliente extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipoCliente::class);
    }
}
