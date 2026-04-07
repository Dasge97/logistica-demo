<?php

namespace App\Modulos\Decisiones\Application\Dto;

final class ResultadoResolucionEntrega
{
    /** @param list<ResultadoOpcionEntrega> $opciones @param list<ResultadoDescarteServicio> $descartes */
    public function __construct(
        public readonly array $opciones,
        public readonly array $descartes,
    ) {
    }
}
