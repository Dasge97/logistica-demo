<?php

namespace App\Modulos\Tarifas\Application;

use App\Modulos\Catalogos\Domain\Entity\NivelServicioEntrega;
use App\Modulos\Catalogos\Domain\Entity\TipoVehiculo;
use App\Modulos\Tarifas\Domain\Entity\ReglaTarifaTransportista;
use App\Modulos\Tarifas\Infrastructure\Persistence\Doctrine\RepositorioReglaTarifaTransportista;

final class ServicioCosteTransportista
{
    public function __construct(private readonly RepositorioReglaTarifaTransportista $repositorioReglaTarifaTransportista)
    {
    }

    public function resolverMejorCoste(
        TipoVehiculo $tipoVehiculo,
        NivelServicioEntrega $nivelServicioEntrega,
        float $distanciaKm,
        int $pesoTotalGramos,
        int $volumenTotalCm3,
    ): ?ResultadoCosteTransportista
    {
        $reglas = $this->repositorioReglaTarifaTransportista->buscarAplicables(
            $tipoVehiculo,
            $nivelServicioEntrega,
            $distanciaKm,
            $pesoTotalGramos,
            $volumenTotalCm3,
        );

        if ([] === $reglas) {
            return null;
        }

        usort($reglas, static fn (ReglaTarifaTransportista $a, ReglaTarifaTransportista $b): int => $a->calcularCosteCentimos($distanciaKm) <=> $b->calcularCosteCentimos($distanciaKm));

        $reglaElegida = $reglas[0];

        return new ResultadoCosteTransportista(
            $reglaElegida,
            $reglaElegida->calcularCosteCentimos($distanciaKm),
            $reglas,
        );
    }
}
