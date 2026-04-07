<?php

namespace App\Modulos\Tarifas\Infrastructure\Persistence\Doctrine;

use App\Modulos\Catalogos\Domain\Entity\NivelServicioEntrega;
use App\Modulos\Catalogos\Domain\Entity\TipoVehiculo;
use App\Modulos\Tarifas\Domain\Entity\ReglaTarifaTransportista;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReglaTarifaTransportista>
 */
final class RepositorioReglaTarifaTransportista extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReglaTarifaTransportista::class);
    }

    public function buscarActiva(TipoVehiculo $tipoVehiculo, NivelServicioEntrega $nivelServicioEntrega): ?ReglaTarifaTransportista
    {
        return $this->findOneBy([
            'tipoVehiculo' => $tipoVehiculo,
            'nivelServicioEntrega' => $nivelServicioEntrega,
            'activa' => true,
        ]);
    }
}
