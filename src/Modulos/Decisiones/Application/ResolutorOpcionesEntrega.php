<?php

namespace App\Modulos\Decisiones\Application;

use App\Modulos\Catalogos\Infrastructure\Persistence\Doctrine\RepositorioNivelServicioEntrega;
use App\Modulos\Catalogos\Infrastructure\Persistence\Doctrine\RepositorioTipoVehiculo;
use App\Modulos\Decisiones\Application\Dto\ResultadoDescarteServicio;
use App\Modulos\Decisiones\Application\Dto\ResultadoOpcionEntrega;
use App\Modulos\Decisiones\Application\Dto\ResultadoResolucionEntrega;
use App\Modulos\Decisiones\Application\Dto\ResultadoVehiculo;
use App\Modulos\Operaciones\Application\ServicioDisponibilidadEntrega;
use App\Modulos\Pedidos\Domain\Entity\Pedido;
use App\Modulos\Tarifas\Application\ServicioCosteTransportista;
use App\Modulos\Tarifas\Application\ServicioTarificacionCliente;

final class ResolutorOpcionesEntrega
{
    public function __construct(
        private readonly RepositorioNivelServicioEntrega $repositorioNivelServicioEntrega,
        private readonly RepositorioTipoVehiculo $repositorioTipoVehiculo,
        private readonly ServicioDisponibilidadEntrega $servicioDisponibilidadEntrega,
        private readonly ServicioTarificacionCliente $servicioTarificacionCliente,
        private readonly ServicioCosteTransportista $servicioCosteTransportista,
        private readonly ValidadorVehiculos $validadorVehiculos,
        private readonly SelectorVehiculoOptimo $selectorVehiculoOptimo,
    ) {
    }

    public function resolverParaPedido(Pedido $pedido): ResultadoResolucionEntrega
    {
        $opciones = [];
        $descartes = [];
        $nivelesServicio = $this->repositorioNivelServicioEntrega->findBy(['activo' => true], ['ordenVisual' => 'ASC', 'nombre' => 'ASC']);

        foreach ($nivelesServicio as $nivelServicio) {
            if (!$this->servicioDisponibilidadEntrega->esViable($nivelServicio, $pedido->getDistanciaKm(), $pedido->getPesoTotalGramos(), $pedido->getVolumenTotalCm3())) {
                $descartes[] = new ResultadoDescarteServicio($nivelServicio, 'No cumple las reglas de disponibilidad operativa.');
                continue;
            }

            $tipoCliente = $pedido->getTipoCliente();
            if (null === $tipoCliente) {
                $descartes[] = new ResultadoDescarteServicio($nivelServicio, 'El pedido no tiene tipo de cliente configurado.');
                continue;
            }

            $precioClienteCentimos = $this->servicioTarificacionCliente->resolverPrecioCentimos($tipoCliente, $nivelServicio, $pedido->getDistanciaKm());
            if (null === $precioClienteCentimos) {
                $descartes[] = new ResultadoDescarteServicio($nivelServicio, 'No existe una tarifa cliente aplicable para este servicio.');
                continue;
            }

            $vehiculos = [];
            foreach ($this->repositorioTipoVehiculo->findBy(['activo' => true], ['nombre' => 'ASC']) as $tipoVehiculo) {
                if (!$this->validadorVehiculos->esCompatible($tipoVehiculo, $pedido->getPesoTotalGramos(), $pedido->getVolumenTotalCm3())) {
                    continue;
                }

                $resultadoCoste = $this->servicioCosteTransportista->resolverMejorCoste(
                    $tipoVehiculo,
                    $nivelServicio,
                    $pedido->getDistanciaKm(),
                    $pedido->getPesoTotalGramos(),
                    $pedido->getVolumenTotalCm3(),
                );
                if (null === $resultadoCoste) {
                    continue;
                }

                $vehiculos[] = new ResultadoVehiculo($tipoVehiculo, $resultadoCoste->costeCentimos, $resultadoCoste->reglaElegida);
            }

            if ([] === $vehiculos) {
                $descartes[] = new ResultadoDescarteServicio($nivelServicio, 'No hay vehiculos compatibles con tarifa transportista activa.');
                continue;
            }

            $vehiculosRentables = array_values(array_filter(
                $vehiculos,
                static fn (ResultadoVehiculo $vehiculo): bool => $vehiculo->costeCentimos <= $precioClienteCentimos,
            ));

            if ([] === $vehiculosRentables) {
                $descartes[] = new ResultadoDescarteServicio($nivelServicio, 'No hay vehiculos rentables para este precio cliente; el servicio no es comercialmente viable.');
                continue;
            }

            $opciones[] = new ResultadoOpcionEntrega(
                $nivelServicio,
                $precioClienteCentimos,
                $vehiculosRentables,
                $this->selectorVehiculoOptimo->seleccionar($vehiculosRentables),
            );
        }

        return new ResultadoResolucionEntrega($opciones, $descartes);
    }
}
