<?php

namespace App\Modulos\Tarifas\Infrastructure\Persistence\Doctrine;

use App\Modulos\Catalogos\Domain\Entity\NivelServicioEntrega;
use App\Modulos\Catalogos\Domain\Entity\TipoCliente;
use App\Modulos\Tarifas\Domain\Entity\ReglaTarifaCliente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReglaTarifaCliente>
 */
final class RepositorioReglaTarifaCliente extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReglaTarifaCliente::class);
    }

    public function buscarAplicable(TipoCliente $tipoCliente, NivelServicioEntrega $nivelServicioEntrega, float $distanciaKm): ?ReglaTarifaCliente
    {
        return $this->createQueryBuilder('regla')
            ->andWhere('regla.tipoCliente = :tipoCliente')
            ->andWhere('regla.nivelServicioEntrega = :nivelServicioEntrega')
            ->andWhere('regla.activa = true')
            ->andWhere(':distanciaKm BETWEEN regla.distanciaDesdeKm AND regla.distanciaHastaKm')
            ->setParameter('tipoCliente', $tipoCliente)
            ->setParameter('nivelServicioEntrega', $nivelServicioEntrega)
            ->setParameter('distanciaKm', $distanciaKm)
            ->orderBy('regla.distanciaHastaKm', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
