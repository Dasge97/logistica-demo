<?php

namespace App\Modulos\Tarifas\Application;

use App\Modulos\Tarifas\Domain\Entity\ReglaTarifaTransportista;

final class ResultadoCosteTransportista
{
    /** @param list<ReglaTarifaTransportista> $reglasAplicables */
    public function __construct(
        public readonly ReglaTarifaTransportista $reglaElegida,
        public readonly int $costeCentimos,
        public readonly array $reglasAplicables,
    ) {
    }
}
