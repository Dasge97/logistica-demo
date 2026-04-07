<?php

namespace App\Modulos\Decisiones\Application;

use App\Modulos\Decisiones\Application\Dto\ResultadoOpcionEntrega;
use App\Modulos\Decisiones\Application\Dto\ResultadoResolucionEntrega;

final class ConstructorExplicacionDecision
{
    public function construir(ResultadoResolucionEntrega $resultadoResolucionEntrega, ResultadoOpcionEntrega $opcionElegida): array
    {
        return [
            'servicio_elegido' => $opcionElegida->nivelServicioEntrega->getNombre(),
            'precio_cliente_centimos' => $opcionElegida->precioClienteCentimos,
            'vehiculo_optimo' => $opcionElegida->vehiculoOptimo?->tipoVehiculo->getNombre(),
            'coste_optimo_centimos' => $opcionElegida->getCosteOptimoCentimos(),
            'margen_centimos' => $opcionElegida->getMargenCentimos(),
            'opciones' => array_map(static fn (ResultadoOpcionEntrega $opcion): array => [
                'servicio' => $opcion->nivelServicioEntrega->getNombre(),
                'precio_cliente_centimos' => $opcion->precioClienteCentimos,
                'vehiculos' => array_map(static fn ($vehiculo): array => [
                    'nombre' => $vehiculo->tipoVehiculo->getNombre(),
                    'coste_centimos' => $vehiculo->costeCentimos,
                ], $opcion->vehiculos),
            ], $resultadoResolucionEntrega->opciones),
            'descartes' => array_map(static fn ($descarte): array => [
                'servicio' => $descarte->nivelServicioEntrega->getNombre(),
                'motivo' => $descarte->motivo,
            ], $resultadoResolucionEntrega->descartes),
        ];
    }
}
