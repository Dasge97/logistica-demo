<?php

namespace App\Modulos\Tarifas\Application;

use App\Modulos\Catalogos\Domain\Entity\NivelServicioEntrega;
use App\Modulos\Catalogos\Domain\Entity\TipoCliente;
use App\Modulos\Tarifas\Infrastructure\Persistence\Doctrine\RepositorioReglaTarifaCliente;

final class ServicioTarificacionCliente
{
    public function __construct(private readonly RepositorioReglaTarifaCliente $repositorioReglaTarifaCliente)
    {
    }

    public function resolverPrecioCentimos(TipoCliente $tipoCliente, NivelServicioEntrega $nivelServicioEntrega, float $distanciaKm): ?int
    {
        $regla = $this->repositorioReglaTarifaCliente->buscarAplicable($tipoCliente, $nivelServicioEntrega, $distanciaKm);

        return $regla?->getPrecioClienteCentimos();
    }
}
