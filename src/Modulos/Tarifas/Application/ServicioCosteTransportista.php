<?php

namespace App\Modulos\Tarifas\Application;

use App\Modulos\Catalogos\Domain\Entity\NivelServicioEntrega;
use App\Modulos\Catalogos\Domain\Entity\TipoVehiculo;
use App\Modulos\Tarifas\Infrastructure\Persistence\Doctrine\RepositorioReglaTarifaTransportista;

final class ServicioCosteTransportista
{
    public function __construct(private readonly RepositorioReglaTarifaTransportista $repositorioReglaTarifaTransportista)
    {
    }

    public function calcularCosteCentimos(TipoVehiculo $tipoVehiculo, NivelServicioEntrega $nivelServicioEntrega, float $distanciaKm): ?int
    {
        $regla = $this->repositorioReglaTarifaTransportista->buscarActiva($tipoVehiculo, $nivelServicioEntrega);

        return $regla?->calcularCosteCentimos($distanciaKm);
    }
}
