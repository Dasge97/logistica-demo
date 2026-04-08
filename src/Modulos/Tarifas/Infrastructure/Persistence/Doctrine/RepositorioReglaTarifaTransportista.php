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

    /** @return list<ReglaTarifaTransportista> */
    public function buscarAplicables(
        TipoVehiculo $tipoVehiculo,
        NivelServicioEntrega $nivelServicioEntrega,
        float $distanciaKm,
        int $pesoTotalGramos,
        int $volumenTotalCm3,
    ): array
    {
        $reglas = $this->findBy([
            'tipoVehiculo' => $tipoVehiculo,
            'nivelServicioEntrega' => $nivelServicioEntrega,
            'activa' => true,
        ], ['createdAt' => 'ASC']);

        return array_values(array_filter(
            $reglas,
            static fn (ReglaTarifaTransportista $regla): bool => $regla->aplicaA($distanciaKm, $pesoTotalGramos, $volumenTotalCm3),
        ));
    }
}
