<?php

namespace App\Modulos\Decisiones\Application\Dto;

use App\Modulos\Catalogos\Domain\Entity\TipoVehiculo;

final class ResultadoVehiculo
{
    public function __construct(
        public readonly TipoVehiculo $tipoVehiculo,
        public readonly int $costeCentimos,
    ) {
    }
}
