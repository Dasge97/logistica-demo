<?php

namespace App\Modulos\Decisiones\Application;

use App\Modulos\Decisiones\Application\Dto\ResultadoVehiculo;

final class SelectorVehiculoOptimo
{
    /** @param list<ResultadoVehiculo> $vehiculos */
    public function seleccionar(array $vehiculos): ?ResultadoVehiculo
    {
        if ([] === $vehiculos) {
            return null;
        }

        usort($vehiculos, static fn (ResultadoVehiculo $a, ResultadoVehiculo $b): int => $a->costeCentimos <=> $b->costeCentimos);

        return $vehiculos[0];
    }
}
