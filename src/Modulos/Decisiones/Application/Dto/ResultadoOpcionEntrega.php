<?php

namespace App\Modulos\Decisiones\Application\Dto;

use App\Modulos\Catalogos\Domain\Entity\NivelServicioEntrega;

final class ResultadoOpcionEntrega
{
    /** @param list<ResultadoVehiculo> $vehiculos */
    public function __construct(
        public readonly NivelServicioEntrega $nivelServicioEntrega,
        public readonly int $precioClienteCentimos,
        public readonly array $vehiculos,
        public readonly ?ResultadoVehiculo $vehiculoOptimo,
    ) {
    }
}
