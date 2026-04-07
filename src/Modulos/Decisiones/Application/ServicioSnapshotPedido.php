<?php

namespace App\Modulos\Decisiones\Application;

use App\Modulos\Decisiones\Application\Dto\ResultadoOpcionEntrega;
use App\Modulos\Decisiones\Application\Dto\ResultadoResolucionEntrega;
use App\Modulos\Decisiones\Domain\Entity\SnapshotTarificacionPedido;
use App\Modulos\Decisiones\Infrastructure\Persistence\Doctrine\RepositorioSnapshotTarificacionPedido;
use App\Modulos\Pedidos\Domain\Entity\Pedido;
use Doctrine\ORM\EntityManagerInterface;

final class ServicioSnapshotPedido
{
    public function __construct(
        private readonly RepositorioSnapshotTarificacionPedido $repositorioSnapshotTarificacionPedido,
        private readonly ConstructorExplicacionDecision $constructorExplicacionDecision,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function crearSnapshot(Pedido $pedido, ResultadoResolucionEntrega $resultadoResolucionEntrega, ResultadoOpcionEntrega $opcionElegida): SnapshotTarificacionPedido
    {
        $existente = $this->repositorioSnapshotTarificacionPedido->findOneBy(['pedido' => $pedido]);
        if ($existente instanceof SnapshotTarificacionPedido) {
            return $existente;
        }

        $snapshot = new SnapshotTarificacionPedido(
            $pedido,
            $opcionElegida->nivelServicioEntrega->getNombre(),
            $opcionElegida->vehiculoOptimo?->tipoVehiculo->getNombre() ?? 'Sin vehiculo',
            $pedido->getDistanciaKm(),
            $pedido->getPesoTotalGramos(),
            $pedido->getVolumenTotalCm3(),
            $opcionElegida->precioClienteCentimos,
            $opcionElegida->getCosteOptimoCentimos() ?? 0,
            $opcionElegida->getMargenCentimos() ?? 0,
            $this->constructorExplicacionDecision->construir($resultadoResolucionEntrega, $opcionElegida),
        );

        $pedido->asignarSnapshotTarificacion($snapshot);
        $this->entityManager->persist($snapshot);

        return $snapshot;
    }
}
