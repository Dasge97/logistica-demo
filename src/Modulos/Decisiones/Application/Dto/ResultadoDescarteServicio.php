<?php

namespace App\Modulos\Decisiones\Application\Dto;

use App\Modulos\Catalogos\Domain\Entity\NivelServicioEntrega;

final class ResultadoDescarteServicio
{
    public function __construct(
        public readonly NivelServicioEntrega $nivelServicioEntrega,
        public readonly string $motivo,
    ) {
    }
}
