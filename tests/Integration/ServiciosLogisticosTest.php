<?php

namespace App\Tests\Integration;

use App\Modulos\Catalogos\Domain\Entity\NivelServicioEntrega;
use App\Modulos\Catalogos\Domain\Entity\TipoCliente;
use App\Modulos\Catalogos\Domain\Entity\TipoVehiculo;
use App\Modulos\Decisiones\Application\ResolutorOpcionesEntrega;
use App\Modulos\Decisiones\Application\SelectorVehiculoOptimo;
use App\Modulos\Decisiones\Application\ValidadorVehiculos;
use App\Modulos\Operaciones\Application\ServicioDisponibilidadEntrega;
use App\Modulos\Operaciones\Domain\Entity\ReglaDisponibilidadServicio;
use App\Modulos\Pedidos\Application\CalculadoraMetricasPedido;
use App\Modulos\Pedidos\Domain\Entity\LineaPedido;
use App\Modulos\Pedidos\Domain\Entity\Pedido;
use App\Modulos\Tarifas\Application\ServicioCosteTransportista;
use App\Modulos\Tarifas\Application\ServicioTarificacionCliente;
use App\Modulos\Tarifas\Domain\Entity\ReglaTarifaCliente;
use App\Modulos\Tarifas\Domain\Entity\ReglaTarifaTransportista;
use PHPUnit\Framework\Assert;

final class ServiciosLogisticosTest extends KernelDatabaseTestCase
{
    public function testCalculaMetricasDelPedido(): void
    {
        [$tipoCliente] = $this->crearCatalogosBase();

        $pedido = new Pedido('TEST-001', 'Cliente Test', '600000001', $tipoCliente);
        $pedido->agregarLinea(new LineaPedido($pedido, 'Caja 1', 2, 1500, 4000));
        $pedido->agregarLinea(new LineaPedido($pedido, 'Caja 2', 1, 500, 1000));

        (new CalculadoraMetricasPedido())->recalcular($pedido);

        Assert::assertSame(3500, $pedido->getPesoTotalGramos());
        Assert::assertSame(9000, $pedido->getVolumenTotalCm3());
    }

    public function testEvaluaDisponibilidadOperativa(): void
    {
        [, $express] = $this->crearCatalogosBase();
        $this->entityManager->persist(new ReglaDisponibilidadServicio($express, 5, 20000, 50000));
        $this->entityManager->flush();

        $servicio = new ServicioDisponibilidadEntrega(
            $this->entityManager->getRepository(ReglaDisponibilidadServicio::class),
        );

        Assert::assertTrue($servicio->esViable($express, 3, 4000, 12000));
        Assert::assertFalse($servicio->esViable($express, 7, 4000, 12000));
    }

    public function testResuelveTarifaClienteYCosteTransportista(): void
    {
        [$tipoCliente, $express, $moto] = $this->crearCatalogosBaseCompletos();

        $this->entityManager->persist(new ReglaTarifaCliente($tipoCliente, $express, 0, 5, 1400));
        $this->entityManager->persist(new ReglaTarifaTransportista($moto, $express, 850, 6, 100));
        $this->entityManager->flush();

        $tarificacion = new ServicioTarificacionCliente(
            $this->entityManager->getRepository(ReglaTarifaCliente::class),
        );
        $coste = new ServicioCosteTransportista(
            $this->entityManager->getRepository(ReglaTarifaTransportista::class),
        );

        Assert::assertSame(1400, $tarificacion->resolverPrecioCentimos($tipoCliente, $express, 3));
        Assert::assertSame(850, $coste->calcularCosteCentimos($moto, $express, 3));
        Assert::assertSame(950, $coste->calcularCosteCentimos($moto, $express, 7));
    }

    public function testResuelveOpcionesYEligeVehiculoOptimo(): void
    {
        [$tipoCliente, $express, $moto, $coche] = $this->crearCatalogosBaseCompletos();

        $this->entityManager->persist(new ReglaDisponibilidadServicio($express, 5, 30000, 150000));
        $this->entityManager->persist(new ReglaTarifaCliente($tipoCliente, $express, 0, 5, 1400));
        $this->entityManager->persist(new ReglaTarifaTransportista($moto, $express, 850, 6, 100));
        $this->entityManager->persist(new ReglaTarifaTransportista($coche, $express, 1200, 10, 120));
        $this->entityManager->flush();

        $pedido = new Pedido('TEST-RESOLVER', 'Cliente Resolver', '600000002', $tipoCliente);
        $pedido->setDistanciaKm(3);
        $pedido->actualizarMetricas(4000, 12000);

        $resultado = (new ResolutorOpcionesEntrega(
            $this->entityManager->getRepository(NivelServicioEntrega::class),
            $this->entityManager->getRepository(TipoVehiculo::class),
            new ServicioDisponibilidadEntrega($this->entityManager->getRepository(ReglaDisponibilidadServicio::class)),
            new ServicioTarificacionCliente($this->entityManager->getRepository(ReglaTarifaCliente::class)),
            new ServicioCosteTransportista($this->entityManager->getRepository(ReglaTarifaTransportista::class)),
            new ValidadorVehiculos(),
            new SelectorVehiculoOptimo(),
        ))->resolverParaPedido($pedido);

        Assert::assertCount(1, $resultado->opciones);
        Assert::assertSame('Express', $resultado->opciones[0]->nivelServicioEntrega->getNombre());
        Assert::assertSame('Moto', $resultado->opciones[0]->vehiculoOptimo?->tipoVehiculo->getNombre());
        Assert::assertSame(550, $resultado->opciones[0]->getMargenCentimos());
    }

    /** @return array{0: TipoCliente, 1: NivelServicioEntrega} */
    private function crearCatalogosBase(): array
    {
        $tipoCliente = new TipoCliente('Particular', 'PARTICULAR');
        $express = new NivelServicioEntrega('Express', 'EXPRESS');

        $this->entityManager->persist($tipoCliente);
        $this->entityManager->persist($express);
        $this->entityManager->flush();

        return [$tipoCliente, $express];
    }

    /** @return array{0: TipoCliente, 1: NivelServicioEntrega, 2: TipoVehiculo, 3: TipoVehiculo} */
    private function crearCatalogosBaseCompletos(): array
    {
        [$tipoCliente, $express] = $this->crearCatalogosBase();
        $moto = new TipoVehiculo('Moto', 'MOTO', 25000, 120000);
        $coche = new TipoVehiculo('Coche', 'COCHE', 120000, 900000);

        $this->entityManager->persist($moto);
        $this->entityManager->persist($coche);
        $this->entityManager->flush();

        return [$tipoCliente, $express, $moto, $coche];
    }
}
